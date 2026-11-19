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
