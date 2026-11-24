# Rebuilds the minified copies (*.min.css / *.min.js) of the site's static assets with esbuild.
#   powershell -ExecutionPolicy Bypass -File tools/build-assets.ps1
# v2_asset() (resources/views/web/home-v2/helpers.php) serves a .min file only while it is NEWER than its source,
# so run this after editing any of the sources below. esbuild is downloaded once into tools/esbuild (git-ignored):
#   https://registry.npmjs.org/@esbuild/win32-x64/-/win32-x64-0.28.2.tgz  (sha512-5ebpxr3nWMzrL/rnUI755Jkuee0bHL/Gq0WTF9lvcpv73wAp5eu8MfBUgWK9bhWvZjj7yX8etf/8tI8Ney695g==)
$ErrorActionPreference = 'Stop'
$root = Split-Path $PSScriptRoot -Parent
$esb = Join-Path $PSScriptRoot 'esbuild\package\esbuild.exe'
if (-not (Test-Path $esb)) { throw "esbuild not found at $esb (see the header of this script for the download)" }

$css = Join-Path $root 'public\assets\css'; $js = Join-Path $root 'public\assets\js'
$sources = @()
$sources += Get-ChildItem $css -Filter '*-v2*.css' | Where-Object { $_.Name -notlike '*.min.css' -and $_.Name -notlike '*.critical.css' }
$sources += Get-ChildItem $js -Filter '*-v2*.js' | Where-Object { $_.Name -notlike '*.min.js' }
foreach ($n in 'custom.css', 'main.css', 'responsive.css') { $p = Join-Path $css $n; if (Test-Path $p) { $sources += Get-Item $p } }
foreach ($n in 'main.js', 'enquiry.js', 'newsletter.js', 'input.js', 'aside.js') { $p = Join-Path $js $n; if (Test-Path $p) { $sources += Get-Item $p } }

$totalIn = 0; $totalOut = 0
foreach ($s in $sources) {
  $out = [IO.Path]::ChangeExtension($s.FullName, 'min' + $s.Extension)
  & $esb $s.FullName --minify --legal-comments=none "--outfile=$out" --log-level=warning
  if ($LASTEXITCODE -ne 0) { throw "esbuild failed on $($s.Name)" }
  (Get-Item $out).LastWriteTime = [DateTime]::Now   # always newer than the source
  $a = $s.Length; $b = (Get-Item $out).Length; $totalIn += $a; $totalOut += $b
  '{0,-34} {1,8} -> {2,8}  ({3:P0})' -f $s.Name, $a, $b, ($b / [Math]::Max(1, $a))
}
'{0} files, {1:N0} KB -> {2:N0} KB' -f $sources.Count, ($totalIn / 1KB), ($totalOut / 1KB)
