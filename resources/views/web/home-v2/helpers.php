<?php
/*
|==========================================================================
| V2 LINK MAPPING — used only by the redesigned (V2) views
|--------------------------------------------------------------------------
| require_once resource_path('views/web/home-v2/helpers.php');
|
| v2_url($urlOrPath)  maps a live URL to its redesigned (V2) page when one exists, otherwise returns it unchanged:
|     /                    -> /home-new                 (route 'home.new')
|     /<page>             -> /<page>-new               when a GET route "<page>-new" exists (services-new, and any future
|                                                       contact-new, blogs-new, about-new … map automatically once created)
|     /<service-path>     -> /v2/<service-path>        when it is a ServiceController page (same rule as the /v2 preview route)
|     anything else        -> unchanged (external links, files, not-yet-redesigned pages)
| v2_links($html)     rewrites same-site <a href> values inside a block of legacy HTML with v2_url().
| Only V2 views call these, so the live site and its templates are untouched.
|==========================================================================
*/
if (!function_exists('v2_url')) {
	function v2_url($target)
	{
		static $cache = [], $uris = null;
		$target = (string) $target;
		/* PASS-THROUGH: the redesigned pages now live on the ORIGINAL URLs, so no mapping is needed (the code below is kept for future preview pages). */
		if (true) return $target;
		if (isset($cache[$target])) return $cache[$target];

		$base = rtrim(\Illuminate\Support\Facades\URL::to('/'), '/');
		$rest = '';
		if (preg_match('/[?#].*$/', $target, $m)) { $rest = $m[0]; $target = substr($target, 0, -strlen($rest)); }
		$orig = $target . $rest;

		if (preg_match('#^https?://#i', $target)) {
			if (stripos($target, $base) !== 0) return $cache[$orig] = $orig; /* external */
			$path = substr($target, strlen($base));
		} else {
			$path = $target;
		}
		$path = trim($path, '/');
		if (strpos($path, 'v2/') === 0 || $path === 'v2' || strpos($path, 'public/') === 0 || preg_match('#\.[a-z0-9]{2,5}$#i', $path)) return $cache[$orig] = $orig;

		if ($uris === null) {
			$uris = [];
			foreach (app('router')->getRoutes()->getRoutes() as $r) {
				if (in_array('GET', $r->methods(), true) && strpos($r->uri(), '{') === false) $uris[trim($r->uri(), '/')] = $r;
			}
		}
		if ($path === '') {
			$out = $orig; /* the redesigned home is served at "/" itself */
		} elseif (isset($uris[$path . '-new'])) {
			$out = $base . '/' . $path . '-new';
		} elseif (isset($uris[$path]) && strpos($uris[$path]->getActionName(), 'ServiceController@') !== false) {
			$out = $base . '/v2/' . $path;
		} elseif (preg_match('#^blogs/([^/]+)$#', $path, $m) && \Illuminate\Support\Facades\Route::has('blogs.new.category')) {
			$out = route('blogs.new.category', [$m[1]]);
		} elseif (preg_match('#^tag/([^/]+)$#', $path, $m) && \Illuminate\Support\Facades\Route::has('blogs.new.tag')) {
			$out = route('blogs.new.tag', [$m[1]]);
		} else {
			$out = $orig;
		}
		if ($out !== $orig) $out .= $rest;
		return $cache[$orig] = $out;
	}
}

if (!function_exists('v2_links')) {
	function v2_links($html)
	{
		return preg_replace_callback('#(<a\b[^>]*?\shref=)(["\'])(.*?)\2#is', function ($m) {
			$href = html_entity_decode($m[3]);
			if ($href === '' || $href[0] === '#' || preg_match('#^(mailto:|tel:|javascript:|whatsapp:)#i', $href)) return $m[0];
			$new = v2_url($href);
			return $new === $href ? $m[0] : $m[1] . $m[2] . e($new) . $m[2];
		}, (string) $html);
	}
}

if (!function_exists('v2_blog_url')) {
	/* blog post link: the redesigned post page when its preview route exists, otherwise the live post URL */
	function v2_blog_url($slug)
	{
		return \Illuminate\Support\Facades\Route::has('blogs.new.detail') ? route('blogs.new.detail', [$slug]) : route('blogs.detail', [$slug]);
	}
}

if (!function_exists('v2_asset')) {
	/* V2 asset URL with ?v=filemtime. When $min is true and a fresh "<name>.min.css|js" build exists (newer than the source),
	   the minified file is served; otherwise the source file. Rebuild the .min files after editing a source asset. */
	function v2_asset($path, $min = true)
	{
		$use = $path;
		if ($min) {
			$m = preg_replace('/\.(css|js)$/', '.min.$1', $path);
			if ($m !== $path && is_file(public_path($m)) && @filemtime(public_path($m)) >= @filemtime(public_path($path))) $use = $m;
		}
		return \Illuminate\Support\Facades\URL::to('/public/' . $use) . '?v=' . @filemtime(public_path($use));
	}
}

if (!function_exists('v2_img')) {
	/* <img> attributes for a project image (path relative to the project root, e.g. "public/storage/blogs/x.jpg"):
	   serves the WebP sibling + responsive srcset (x-w480/960/1440.webp) when they exist, plus intrinsic width/height.
	   Falls back to the original file (or $fallback URL when the image is missing). */
	function v2_img($rel, $sizes = '100vw', $fallback = null)
	{
		if ($fallback === null) $fallback = \Illuminate\Support\Facades\URL::to('/public/og-logo.jpeg');
		$rel = ltrim((string) $rel, '/');
		$file = base_path($rel);
		if ($rel === '' || substr($rel, -1) === '/' || !is_file($file)) return 'src="' . e($fallback) . '"';
		$base = preg_replace('/\.(jpe?g|png)$/i', '', $rel);
		$url = function ($p) { return \Illuminate\Support\Facades\URL::to('/' . $p); };
		$out = [];
		if ($base !== $rel && is_file(base_path($base . '.webp'))) {
			$out[] = 'src="' . e($url($base . '.webp')) . '"';
			$set = [];
			foreach ([480, 960, 1440] as $w) { if (is_file(base_path($base . '-w' . $w . '.webp'))) $set[] = $url($base . '-w' . $w . '.webp') . ' ' . $w . 'w'; }
			if ($set) { $out[] = 'srcset="' . e(implode(', ', $set)) . '"'; $out[] = 'sizes="' . e($sizes) . '"'; }
		} else {
			$out[] = 'src="' . e($url($rel)) . '"';
		}
		$dim = @getimagesize($file);
		if ($dim) { $out[] = 'width="' . (int) $dim[0] . '"'; $out[] = 'height="' . (int) $dim[1] . '"'; }
		$out[] = 'decoding="async"';
		return implode(' ', $out);
	}
}

if (!function_exists('v2_clients')) {
	/* Client brands for the logo marquee, from config/clients.php (same list the old elements/clients.blade.php uses).
	   Returns [imageNumber => ['name' => 'Nike', 'src' => '<url>', 'alt' => 'Nike logo – DataMySite client']].
	   Uses the .webp logo when it exists, otherwise the .png (logos added later may not have a .webp yet). */
	function v2_clients()
	{
		static $ext = [];   /* only the file lookup is cached: the URL must follow the current host on every call */
		$list = [];
		foreach ((array) config('clients', []) as $no => $name) {
			if (!isset($ext[$no])) $ext[$no] = is_file(public_path('assets/img/clients-final/' . $no . '.webp')) ? 'webp' : 'png';
			$list[$no] = [
				'name' => $name,
				'src'  => \Illuminate\Support\Facades\URL::to('/public/assets/img/clients-final/' . $no . '.' . $ext[$no]),
				'alt'  => $name . ' logo – DataMySite client',
			];
		}
		return $list;
	}
}

if (!function_exists('seo_canonical')) {
	/* Canonical URL of the current page: the URL itself (same host + scheme as the request, so staging points at staging and
	   production at production), without query string or trailing slash. On the production domain it is always
	   https://datamysite.com (never http:// or www.). Paginated lists keep ?page=N for N > 1. */
	function seo_canonical()
	{
		$url = \Illuminate\Support\Facades\URL::current();
		$url = preg_replace('#^https?://(?:www\.)?datamysite\.com#i', 'https://datamysite.com', $url);
		if (strlen($url) > 1 && substr($url, -1) === '/' && parse_url($url, PHP_URL_PATH) !== '/') $url = rtrim($url, '/');
		$page = (int) request('page');
		return $page > 1 ? $url . '?page=' . $page : $url;
	}
}

if (!function_exists('v2_faq_schema')) {
	/* FAQPage JSON-LD from a list of [question, answerHtml] pairs — the same list that renders the visible FAQ, so markup and
	   structured data never drift apart. Returns '' when there is nothing to describe. */
	function v2_faq_schema(array $pairs)
	{
		if (!config('seo.template_schema', true)) return '';
		$clean = function ($s) { return trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $s), ENT_QUOTES | ENT_HTML5, 'UTF-8'))); };
		$items = [];
		foreach ($pairs as $p) {
			$q = $clean($p[0] ?? ''); $a = $clean($p[1] ?? '');
			if ($q === '' || $a === '') continue;
			$items[] = ['@type' => 'Question', 'name' => $q, 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $a]];
		}
		if (!$items) return '';
		$json = json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $items], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);
		return '<script type="application/ld+json">' . $json . '</script>';
	}
}

if (!function_exists('v2_snippet_has')) {
	/* Does a database snippet (table snippet_code, shown on this page by ViewServiceProvider as $headSnippet / $bodySnippet) already
	   carry JSON-LD of this @type? Page-specific snippets are keyed by the production URL, so they load on production but not on
	   staging. The template adds its own schema only when no snippet has that type, so a page never ends up with two FAQPage /
	   Service / BreadcrumbList / Organization blocks (Google treats duplicate FAQPage as an error). */
	function v2_snippet_has($type, $head = null, $body = null)
	{
		foreach ([$head, $body] as $list) {
			foreach ((is_iterable($list) ? $list : []) as $snip) {
				$code = (string) ($snip->snippet_code ?? '');
				if ($code !== '' && preg_match('/"@type"\s*:\s*(?:"' . preg_quote($type, '/') . '"|\[[^\]]*"' . preg_quote($type, '/') . '")/i', $code)) return true;
			}
		}
		return false;
	}
}

if (!function_exists('v2_biz')) {
	/* Business detail from config/business.php (phone, email, address ...), e.g. v2_biz('email'), v2_biz('address.locality'). */
	function v2_biz($key, $default = '')
	{
		return config('business.' . $key, $default);
	}
}

if (!function_exists('v2_wa_url')) {
	/* WhatsApp chat link for the business number. Optional $text pre-fills the message. */
	function v2_wa_url($text = null)
	{
		return 'https://api.whatsapp.com/send/?phone=' . config('business.whatsapp')
			. ($text !== null && $text !== '' ? '&text=' . rawurlencode($text) : '');
	}
}

if (!function_exists('v2_case_studies')) {
	/* Case studies for the V2 pages, from config/case_studies.php (PDFs in public/case-studies/v2/, covers in public/case-studies/v2/cover/).
	   Returns a list of ['name', 'pdf_url', 'cover' (path for v2_img()), 'cover_url', 'alt']. URLs are built per call so they follow the current host. */
	function v2_case_studies()
	{
		$out = [];
		foreach ((array) config('case_studies', []) as $c) {
			$cover = 'public/case-studies/v2/cover/' . $c['cover'] . '.jpg';
			$out[] = [
				'name'      => $c['name'],
				'pdf_url'   => \Illuminate\Support\Facades\URL::to('/public/case-studies/v2/' . rawurlencode($c['pdf']) . '.pdf'),
				'cover'     => $cover,
				'cover_url' => \Illuminate\Support\Facades\URL::to('/' . $cover),
				'alt'       => $c['name'] . ' × DMS case study cover',
			];
		}
		return $out;
	}
}
