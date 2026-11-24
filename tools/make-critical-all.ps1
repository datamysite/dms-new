# Regenerates the critical CSS of every V2 page (run tools/build-assets.ps1 first, then this; run again after editing any stylesheet).
#   powershell -ExecutionPolicy Bypass -File tools/make-critical-all.ps1 [-Base http://localhost/datamysite]
param([string]$Base = 'http://localhost/datamysite')
$pages = @(@('home', '/'), @('about', '/about-us'), @('contact', '/contact-us'), @('faq', '/faq'), @('blogs', '/blogs'), @('blogs-categories', '/blogs/categories'),
  @('case-studies', '/client-case-studies'), @('privacy', '/privacy-policy'), @('terms', '/terms-conditions'), @('services-landing', '/services'), @('service', '/seo-services'))
$port = 9600
foreach ($p in $pages) {
  $port++
  "== $($p[0])"
  & powershell -ExecutionPolicy Bypass -File (Join-Path $PSScriptRoot 'make-critical.ps1') -Name $p[0] -Url ($Base.TrimEnd('/') + $p[1]) -Port $port | Select-Object -Last 1
}
