<?php
/**
 * T39 - IndexNow bulk submitter for datamysite.com
 * Reads the live sitemap, submits ALL URLs to IndexNow in ONE request.
 * No daily limit (IndexNow accepts up to 10,000 URLs per call).
 * Feeds: Bing, Yandex, Seznam, Naver (NOT Google - see notes).
 *
 * SETUP (one time):
 *   1. Upload the {KEY}.txt file to /public_html (site root) so it is
 *      reachable at https://datamysite.com/{KEY}.txt
 *   2. Upload this file to /public_html as indexnow_submit.php
 * RUN:  https://datamysite.com/indexnow_submit.php?token=dms2026
 *   or CLI:  php indexnow_submit.php
 * RE-RUN any time you publish new blogs - safe to repeat.
 */
$KEY   = 'e6c753d0849e4c45a1493efd559b3550';           // <- key inserted below automatically
$HOST  = 'datamysite.com';
$TOKEN = 'dms2026';                    // simple guard for browser runs

if (php_sapi_name() !== 'cli' && (($_GET['token'] ?? '') !== $TOKEN)) { http_response_code(403); exit('forbidden'); }
header('Content-Type: text/plain');

// --- 1. Collect URLs from sitemap (handles sitemap index too) ---
function fetch($u){ $c=curl_init($u); curl_setopt_array($c,[CURLOPT_RETURNTRANSFER=>1,CURLOPT_FOLLOWLOCATION=>1,CURLOPT_TIMEOUT=>30,CURLOPT_USERAGENT=>'DMS-IndexNow/1.0']); $r=curl_exec($c); curl_close($c); return $r; }
$urls = [];
$queue = ["https://$HOST/sitemap.xml"];
while ($queue) {
    $xmlRaw = fetch(array_shift($queue));
    if (!$xmlRaw) continue;
    $xml = @simplexml_load_string($xmlRaw);
    if (!$xml) continue;
    if ($xml->getName() === 'sitemapindex') { foreach ($xml->sitemap as $s) $queue[] = (string)$s->loc; }
    else { foreach ($xml->url as $u) $urls[] = (string)$u->loc; }
}
$urls = array_values(array_unique($urls));
echo "Sitemap URLs found: " . count($urls) . "\n";
if (!$urls) exit("No URLs - check sitemap.xml exists.\n");

// --- 2. Verify key file is reachable (IndexNow rejects otherwise) ---
$keyCheck = trim(fetch("https://$HOST/$KEY.txt"));
if ($keyCheck !== $KEY) exit("KEY FILE NOT REACHABLE at https://$HOST/$KEY.txt - upload it first.\n");
echo "Key file verified.\n";

// --- 3. Submit ALL URLs in one POST ---
$payload = json_encode(['host'=>$HOST,'key'=>$KEY,'keyLocation'=>"https://$HOST/$KEY.txt",'urlList'=>$urls], JSON_UNESCAPED_SLASHES);
$c = curl_init('https://api.indexnow.org/indexnow');
curl_setopt_array($c,[CURLOPT_POST=>1,CURLOPT_POSTFIELDS=>$payload,CURLOPT_HTTPHEADER=>['Content-Type: application/json; charset=utf-8'],CURLOPT_RETURNTRANSFER=>1,CURLOPT_TIMEOUT=>60]);
$resp = curl_exec($c); $code = curl_getinfo($c,CURLINFO_HTTP_CODE); curl_close($c);
echo "IndexNow HTTP $code\n";
echo ($code==200||$code==202) ? "SUCCESS - " . count($urls) . " URLs submitted.\n" : "Response: $resp\n";
echo "Codes: 200/202=accepted | 403=key mismatch | 422=bad URLs | 429=slow down\n";
