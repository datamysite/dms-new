# Resizes many images to WebP in ONE headless Chrome session (see png-to-webp.ps1 for a single file).
#   powershell -ExecutionPolicy Bypass -File tools/webp-batch.ps1 -Dir public/assets/img/clients-final -Source webp -Width 256 -Suffix -w256
# For every <name>.<Source> in Dir it writes <name><Suffix>.webp (never overwrites the original).
param(
  [Parameter(Mandatory)][string]$Dir, [string]$Source = 'webp', [Parameter(Mandatory)][int]$Width, [string]$Suffix = '',
  [double]$Quality = 0.82, [string]$Base = 'http://localhost/datamysite', [int]$Port = 9478
)
$ErrorActionPreference = 'Stop'
$root = Split-Path $PSScriptRoot -Parent
$abs = Join-Path $root $Dir
$rel = ($Dir -replace '\\', '/').Trim('/')
$files = Get-ChildItem $abs -Filter "*.$Source" | Where-Object { $_.Name -notmatch '-w\d+\.' }
$chrome = 'C:\Program Files\Google\Chrome\Application\chrome.exe'
$prof = Join-Path $env:TEMP ('wb-prof-' + [guid]::NewGuid().ToString('N').Substring(0, 8))
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
  $saved = 0
  foreach ($f in $files) {
    $js = "(async function(){var img=new Image();img.crossOrigin='anonymous';img.src='$Base/$rel/$($f.Name)';await img.decode();var w=Math.min($Width,img.naturalWidth),h=Math.round(img.naturalHeight*w/img.naturalWidth);var c=document.createElement('canvas');c.width=w;c.height=h;var x=c.getContext('2d');x.imageSmoothingQuality='high';x.drawImage(img,0,0,w,h);return c.toDataURL('image/webp',$Quality);})()"
    $d = (Send-Cdp 'Runtime.evaluate' @{ expression = $js; returnByValue = $true; awaitPromise = $true }).result.result.value
    if ($d -notmatch '^data:image/webp;base64,') { Write-Warning "skipped $($f.Name)"; continue }
    $out = Join-Path $abs ($f.BaseName + $Suffix + '.webp')
    [IO.File]::WriteAllBytes($out, [Convert]::FromBase64String($d.Substring(23)))
    $saved += $f.Length - (Get-Item $out).Length
  }
  "{0} files -> {1}*{2}.webp, {3:N0} KB smaller in total" -f $files.Count, $Dir, $Suffix, ($saved / 1KB)
  $ws.Dispose()
} finally {
  Get-CimInstance Win32_Process -Filter "Name='chrome.exe'" | Where-Object { $_.CommandLine -like "*$prof*" } | ForEach-Object { Stop-Process -Id $_.ProcessId -Force -ErrorAction SilentlyContinue }
  Start-Sleep -Milliseconds 400
  Remove-Item -Recurse -Force $prof -ErrorAction SilentlyContinue
}
