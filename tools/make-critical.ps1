# Generates the critical (first-screen) CSS of a V2 page with headless Chrome, no Node needed.
#   powershell -ExecutionPolicy Bypass -File tools/make-critical.ps1 -Name home -Url http://localhost/datamysite/
# Output: public/assets/css/<Name>.critical.css  (minified with esbuild; inlined by web/home-v2/master.blade.php when the page
# declares @section('v2crit', 'assets/css/<Name>.critical.css') and the file is newer than every stylesheet).
# How: the page is loaded WITHOUT its critical file (so every stylesheet is present), at a phone and a desktop size; every rule of every
# linked stylesheet is kept when one of its selectors matches an element that is visible in the first screen (html.hv2-loading and
# .hv2-leaving are forced on so the loader overlay rules are kept). The union of both sizes is written in the original cascade order.
param(
  [Parameter(Mandatory)][string]$Name,
  [Parameter(Mandatory)][string]$Url,
  [int]$Port = 9455
)
$ErrorActionPreference = 'Stop'
$root = Split-Path $PSScriptRoot -Parent
$outFile = Join-Path $root "public\assets\css\$Name.critical.css"
$esb = Join-Path $PSScriptRoot 'esbuild\package\esbuild.exe'
if (Test-Path $outFile) { Remove-Item $outFile -Force }   # the page must render with all its stylesheets while we measure
$chrome = 'C:\Program Files\Google\Chrome\Application\chrome.exe'
$prof = Join-Path $env:TEMP ('crit-prof-' + [guid]::NewGuid().ToString('N').Substring(0, 8))

$collect = @'
(function () {
  /* 1.6 screens, not 1: a first-screen box (the hero form card) also contains content below the fold; without its rules the box
     changes height when the full stylesheets arrive = a layout shift */
  var vh = innerHeight * 1.6, vw = innerWidth, items = [], frames = {};
  var de = document.documentElement; de.classList.add('hv2-loading', 'hv2-leaving');
  var home = (document.querySelector('meta[name="home_url"]') || {}).content || location.origin;
  var pub = home.replace(/\/$/, '') + '/public';
  var pad = function (n, w) { n = String(n); while (n.length < w) n = '0' + n; return n; };
  /* hidden-until-used UI (mobile drawer, mega menu, popups, floating buttons) is always kept: its base "hidden" rules must exist
     at first paint, otherwise it shows up unstyled in the page flow until the full stylesheets arrive */
  var KEEP = '.hs-header, .hs-scrim, .hs-drawer, .modal, .modal-backdrop, #ov, .whatsapp-chat, #dms-root, #dms-teaser, .hv2-top, .hv2-progress';
  var visible = function (el) {
    var r = el.getBoundingClientRect();
    if (r.width > 0 && r.height > 0 && r.top < vh && r.bottom > 0 && r.left < vw && r.right > 0) return true;
    if (el.matches(KEEP)) return true;
    /* an element that is display:none at this size (e.g. the desktop nav on a phone) has no box: judge it by the nearest ancestor
       that has one, otherwise the very rule that hides it would be dropped from the critical CSS */
    if (r.width === 0 && r.height === 0) {
      var p = el.parentElement;
      while (p && p !== document.body) {
        var pr = p.getBoundingClientRect();
        if (pr.width > 0 || pr.height > 0) return pr.top < vh && pr.bottom > 0 && pr.left < vw && pr.right > 0;
        p = p.parentElement;
      }
    }
    return false;   /* the hidden container only: while it is hidden its contents are not seen, so they need no first-paint rules */
  };
  var split = function (sel) {
    var out = [], d = 0, cur = '', q = '';
    for (var i = 0; i < sel.length; i++) {
      var c = sel[i];
      if (q) { cur += c; if (c === q) q = ''; continue; }
      if (c === '"' || c === "'") { q = c; cur += c; continue; }
      if (c === '(' || c === '[') d++; else if (c === ')' || c === ']') d--;
      if (c === ',' && d === 0) { out.push(cur); cur = ''; } else cur += c;
    }
    if (cur.trim()) out.push(cur);
    return out;
  };
  var matches = function (selText) {
    var parts = split(selText);
    for (var i = 0; i < parts.length; i++) {
      var s = parts[i].replace(/::?(before|after|first-line|first-letter|placeholder|selection|marker|-[a-z-]+)(\([^)]*\))?/gi, '')
                      .replace(/:(hover|focus|focus-visible|focus-within|active|visited|target|disabled|checked|invalid|valid)\b/gi, '')
                      .trim();
      if (!s) s = '*';
      try {
        var list = document.querySelectorAll(s);
        for (var k = 0; k < list.length; k++) if (visible(list[k])) return true;
        if (list.length && /^(html|body|:root|\*)$/.test(s)) return true;
      } catch (e) { /* selector the browser cannot parse: not needed for first paint */ }
    }
    return false;
  };
  var fixUrls = function (css, base) {
    return css.replace(/url\(\s*(['"]?)([^'")]+)\1\s*\)/g, function (m, q, u) {
      if (/^(data:|#)/.test(u)) return m;
      var abs; try { abs = new URL(u, base).href; } catch (e) { return m; }
      if (abs.indexOf(pub) === 0) abs = '__PUBLIC__' + abs.slice(pub.length);
      return 'url("' + abs + '")';
    });
  };
  var walk = function (rules, key, wrap, base) {
    for (var i = 0; i < rules.length; i++) {
      var r = rules[i], k = key + '.' + pad(i, 5);
      if (r.type === 1) { if (matches(r.selectorText)) items.push({ k: k, w: wrap, h: base.split('/').pop(), css: fixUrls(r.cssText, base) }); }
      else if (r.type === 4) { if (matchMedia(r.conditionText || r.media.mediaText).matches) walk(r.cssRules, k, wrap + '@media ' + r.media.mediaText + '|', base); }
      else if (r.type === 12) { if (CSS.supports(r.conditionText)) walk(r.cssRules, k, wrap + '@supports ' + r.conditionText + '|', base); }
      else if (r.type === 7) { frames[r.name] = r.cssText; }
      else if (r.constructor && r.constructor.name === 'CSSPropertyRule') { items.push({ k: k, w: wrap, h: base.split('/').pop(), css: r.cssText }); }   /* @property registrations (loader) are needed at first paint */
      /* @font-face / @import / @page: not first-paint rules (fonts and the icon subset are inlined by the page itself) */
    }
  };
  for (var s = 0; s < document.styleSheets.length; s++) {
    var sh = document.styleSheets[s];
    if (!sh.href) continue;                                   /* inline <style> blocks stay inline in the page */
    var rules; try { rules = sh.cssRules; } catch (e) { continue; }
    walk(rules, pad(s, 3), '', sh.href);
  }
  return JSON.stringify({ items: items, frames: frames });
})()
'@

$proc = Start-Process $chrome -ArgumentList @('--headless=new', '--disable-gpu', "--remote-debugging-port=$Port", "--user-data-dir=`"$prof`"", '--window-size=1440,900', 'about:blank') -PassThru
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
  $null = Send-Cdp 'Page.enable' @{}
  $all = @{}; $frames = @{}
  foreach ($vp in @(@(390, 844, $true), @(1440, 900, $false), @(820, 1100, $false))) {
    $null = Send-Cdp 'Emulation.setDeviceMetricsOverride' @{ width = $vp[0]; height = $vp[1]; deviceScaleFactor = 1; mobile = $vp[2] }
    $null = Send-Cdp 'Page.navigate' @{ url = $Url }
    for ($i = 0; $i -lt 150; $i++) { Start-Sleep -Milliseconds 200; if ((Send-Cdp 'Runtime.evaluate' @{ expression = 'document.readyState'; returnByValue = $true }).result.result.value -eq 'complete') { break } }
    Start-Sleep -Milliseconds 3500   # loader gone, phone widget built
    $res = (Send-Cdp 'Runtime.evaluate' @{ expression = $collect; returnByValue = $true }).result.result.value
    if (-not $res) { throw "no result for width $($vp[0])" }
    $data = $res | ConvertFrom-Json
    foreach ($it in $data.items) { $all[$it.k] = $it }
    foreach ($p in $data.frames.PSObject.Properties) { $frames[$p.Name] = $p.Value }
    "{0}px: {1} rules kept so far" -f $vp[0], $all.Count
  }
  $ws.Dispose()

  # assemble in cascade order; consecutive rules with the same @media/@supports wrapper share one block
  $sbOut = New-Object Text.StringBuilder
  $curWrap = ''
  foreach ($k in ($all.Keys | Sort-Object)) {
    $it = $all[$k]
    if ($it.w -ne $curWrap) {
      if ($curWrap) { [void]$sbOut.Append(('}' * @($curWrap.TrimEnd('|') -split '\|').Count)) }
      if ($it.w) { foreach ($c in ($it.w.TrimEnd('|') -split '\|')) { [void]$sbOut.Append($c + '{') } }
      $curWrap = $it.w
    }
    [void]$sbOut.Append($it.css + "`n")
  }
  if ($curWrap) { [void]$sbOut.Append(('}' * @($curWrap.TrimEnd('|') -split '\|').Count)) }
  $all.Values | Group-Object h | ForEach-Object { "{0,-28} {1,4} rules {2,7:N1} KB" -f $_.Name, $_.Count, (($_.Group | ForEach-Object { $_.css.Length } | Measure-Object -Sum).Sum / 1KB) }
  $css = $sbOut.ToString()
  foreach ($n in $frames.Keys) { if ($css.Contains($n)) { $css += "`n" + $frames[$n] } }   # @keyframes that kept rules use

  $tmp = Join-Path $env:TEMP "$Name.critical.raw.css"
  [IO.File]::WriteAllText($tmp, $css, (New-Object Text.UTF8Encoding($false)))
  & $esb $tmp --minify "--outfile=$outFile" --log-level=warning
  if ($LASTEXITCODE -ne 0) { throw 'esbuild failed on the critical CSS' }
  Remove-Item $tmp -Force
  $size = (Get-Item $outFile).Length
  "{0}: {1:N1} KB (raw {2:N1} KB)" -f $outFile, ($size / 1KB), ($css.Length / 1KB)
  if ($size -gt 70KB) { Write-Warning 'critical CSS is large (> 70 KB): check which rules were kept' }
} finally {
  Get-CimInstance Win32_Process -Filter "Name='chrome.exe'" | Where-Object { $_.CommandLine -like "*$prof*" } | ForEach-Object { Stop-Process -Id $_.ProcessId -Force -ErrorAction SilentlyContinue }
  Start-Sleep -Milliseconds 500
  Remove-Item -Recurse -Force $prof -ErrorAction SilentlyContinue
}
