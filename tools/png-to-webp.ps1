# Converts/resizes a PNG to WebP with headless Chrome's canvas encoder (no Node / cwebp needed).
#   powershell -ExecutionPolicy Bypass -File tools/png-to-webp.ps1 -In public/assets/img/logo-white.png -Out public/assets/img/logo-white.webp -Width 200 [-Quality 0.9]
# The PNG is read through the local dev site (http://localhost/datamysite/<path under the project root>).
param(
  [Parameter(Mandatory)][string]$In, [Parameter(Mandatory)][string]$Out, [Parameter(Mandatory)][int]$Width,
  [double]$Quality = 0.9, [string]$Base = 'http://localhost/datamysite', [int]$Port = 9477
)
$ErrorActionPreference = 'Stop'
$root = Split-Path $PSScriptRoot -Parent
$rel = ($In -replace '\\', '/').TrimStart('/')
$outPath = if ([IO.Path]::IsPathRooted($Out)) { $Out } else { Join-Path $root $Out }
$chrome = 'C:\Program Files\Google\Chrome\Application\chrome.exe'
$prof = Join-Path $env:TEMP ('webp-prof-' + [guid]::NewGuid().ToString('N').Substring(0, 8))
$proc = Start-Process $chrome -ArgumentList @('--headless=new', '--disable-gpu', "--remote-debugging-port=$Port", "--user-data-dir=`"$prof`"", 'about:blank') -PassThru
try {
  $raw = $null
  for ($i = 0; $i -lt 50 -and -not $raw; $i++) { try { $raw = (Invoke-WebRequest -UseBasicParsing "http://127.0.0.1:$Port/json").Content } catch { Start-Sleep -Milliseconds 200 } }
  $wsUrl = $null
  foreach ($t in @($raw | ConvertFrom-Json)) { foreach ($tab in $t) { if (-not $wsUrl -and $tab.type -eq 'page') { $wsUrl = $tab.webSocketDebuggerUrl } } }
  $ws = New-Object System.Net.WebSockets.ClientWebSocket
  [void]$ws.ConnectAsync([Uri]$wsUrl, [Threading.CancellationToken]::None).GetAwaiter().GetResult()
  $script:id = 0
  function Send-Cdp($method, $params) {
    $script:id++; $myId = $script:id
    $msg = @{ id = $myId; method = $method; params = $params } | ConvertTo-Json -Depth 8 -Compress
    $bytes = [Text.Encoding]::UTF8.GetBytes($msg)
    [void]$ws.SendAsync((New-Object ArraySegment[byte] (,$bytes)), 'Text', $true, [Threading.CancellationToken]::None).GetAwaiter().GetResult()
    while ($true) {
      $sb = New-Object Text.StringBuilder; $buf = New-Object byte[] 262144
      do { $r = $ws.ReceiveAsync((New-Object ArraySegment[byte] (,$buf)), [Threading.CancellationToken]::None).GetAwaiter().GetResult(); [void]$sb.Append([Text.Encoding]::UTF8.GetString($buf, 0, $r.Count)) } while (-not $r.EndOfMessage)
      $obj = $sb.ToString() | ConvertFrom-Json
      if ($obj.id -eq $myId) { return $obj }
    }
  }
  $null = Send-Cdp 'Page.navigate' @{ url = "$Base/" }
  Start-Sleep -Milliseconds 1500
  $imgUrl = "$Base/$rel"
  $js = "(async function(){var img=new Image();img.crossOrigin='anonymous';img.src='$imgUrl';await img.decode();var w=$Width,h=Math.round(img.naturalHeight*w/img.naturalWidth);var c=document.createElement('canvas');c.width=w;c.height=h;var x=c.getContext('2d');x.imageSmoothingQuality='high';x.drawImage(img,0,0,w,h);return JSON.stringify({w:w,h:h,d:c.toDataURL('image/webp',$Quality)});})()"
  $res = (Send-Cdp 'Runtime.evaluate' @{ expression = $js; returnByValue = $true; awaitPromise = $true }).result.result.value
  if (-not $res) { throw 'conversion failed (is the dev site running?)' }
  $o = $res | ConvertFrom-Json
  if ($o.d -notmatch '^data:image/webp;base64,') { throw 'Chrome did not produce a WebP' }
  [IO.File]::WriteAllBytes($outPath, [Convert]::FromBase64String($o.d.Substring('data:image/webp;base64,'.Length)))
  "{0}: {1}x{2}, {3:N1} KB" -f $outPath, $o.w, $o.h, ((Get-Item $outPath).Length / 1KB)
  $ws.Dispose()
} finally {
  Get-CimInstance Win32_Process -Filter "Name='chrome.exe'" | Where-Object { $_.CommandLine -like "*$prof*" } | ForEach-Object { Stop-Process -Id $_.ProcessId -Force -ErrorAction SilentlyContinue }
  Start-Sleep -Milliseconds 400
  Remove-Item -Recurse -Force $prof -ErrorAction SilentlyContinue
}
