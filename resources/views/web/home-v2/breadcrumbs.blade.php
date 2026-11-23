{{--
|==========================================================================
| V2 BREADCRUMBS — shared component (mandatory on every redesigned page except the homepage)
|--------------------------------------------------------------------------
| @include('web.home-v2.breadcrumbs', ['crumbs' => [['Home', url], ['Services', url], ['Current page', null]]])
|   $crumbs      ordered list of [label, url|null]; the last item is the current page (not linked)
|   $crumbSchema (optional, default true) set false when the page already outputs a BreadcrumbList
| Styles: .hv2-crumbs in public/assets/css/home-v2.css. Visible trail + BreadcrumbList JSON-LD come from the same list.
|==========================================================================
--}}
@php
	require_once resource_path('views/web/home-v2/helpers.php');
	$hv2Crumbs = array_values($crumbs ?? []);
	$hv2CrumbLast = count($hv2Crumbs) - 1;
@endphp
@if($hv2CrumbLast > 0)
<nav class="hv2-crumbs" aria-label="Breadcrumb" data-reveal="up">
	<ol>
		@foreach($hv2Crumbs as $i => $c)
			<li>
				@if($i < $hv2CrumbLast && !empty($c[1]))
					<a href="{{ v2_url($c[1]) }}">{{ $c[0] }}</a>
				@else
					<span aria-current="page">{{ $c[0] }}</span>
				@endif
			</li>
		@endforeach
	</ol>
</nav>
@if(($crumbSchema ?? true) && config('seo.template_schema', true) && !v2_snippet_has('BreadcrumbList', $headSnippet ?? null, $bodySnippet ?? null))
<script type="application/ld+json">{!! json_encode([
	'@context' => 'https://schema.org',
	'@type' => 'BreadcrumbList',
	'itemListElement' => array_map(function ($c, $i) {
		$n = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $c[0]];
		if (!empty($c[1])) $n['item'] = $c[1];
		return $n;
	}, $hv2Crumbs, array_keys($hv2Crumbs)),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif
@endif
