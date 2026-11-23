{{-- Site-wide structured data (JSON-LD): Organization + WebSite on every page, plus ProfessionalService (local business) on
     home and contact. All facts come from config/business.php. The ids (/#organization, /#website) are the ones the blog
     schema already refers to. Placed in <head> by home-v2/master.blade.php; not delayed (JSON-LD never blocks rendering). --}}
@if(config('seo.template_schema', true))
@php
	$scBiz = config('business');
	$scRoot = rtrim(url('/'), '/');
	$scOrgId = $scRoot . '/#organization';
	$scAddress = [
		'@type' => 'PostalAddress',
		'streetAddress' => $scBiz['address']['street'],
		'addressLocality' => $scBiz['address']['locality'],
		'addressCountry' => $scBiz['address']['country'],
	];
	/* a database snippet (production URL) may already describe the organisation / website / local business: do not add a second one */
	$scHas = function ($t) use ($headSnippet, $bodySnippet) { return v2_snippet_has($t, $headSnippet ?? null, $bodySnippet ?? null); };
	$scGraph = [
		[
			'@type' => 'Organization',
			'@id' => $scOrgId,
			'name' => $scBiz['name'],
			'legalName' => $scBiz['legal_name'],
			'url' => $scRoot . '/',
			'logo' => ['@type' => 'ImageObject', 'url' => url('/' . ltrim($scBiz['logo'], '/'))],
			'description' => $scBiz['description'],
			'email' => $scBiz['email'],
			'telephone' => $scBiz['phone'],
			'address' => $scAddress,
			'areaServed' => $scBiz['area_served'],
			'sameAs' => $scBiz['same_as'],
			'contactPoint' => [['@type' => 'ContactPoint', 'contactType' => 'customer service', 'telephone' => $scBiz['phone'], 'email' => $scBiz['email'], 'areaServed' => 'AE', 'availableLanguage' => ['English', 'Arabic']]],
		],
		[
			'@type' => 'WebSite',
			'@id' => $scRoot . '/#website',
			'url' => $scRoot . '/',
			'name' => $scBiz['name'],
			'inLanguage' => 'en',
			'publisher' => ['@id' => $scOrgId],
		],
	];
	/* local business details: home + contact only (the pages that show the office address and hours) */
	if (request()->is('/') || request()->is('contact-us')) {
		$scHours = [];
		foreach ($scBiz['hours'] as $h) { $scHours[] = ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => $h['days'], 'opens' => $h['opens'], 'closes' => $h['closes']]; }
		$scLocal = [
			'@type' => 'ProfessionalService',
			'@id' => $scRoot . '/#localbusiness',
			'name' => $scBiz['name'],
			'url' => $scRoot . '/',
			'image' => url('/public/og-image-1200x630.jpg'),
			'telephone' => [$scBiz['phone'], $scBiz['telephone']],
			'email' => $scBiz['email'],
			'address' => $scAddress,
			'areaServed' => $scBiz['area_served'],
			'openingHoursSpecification' => $scHours,
			'parentOrganization' => ['@id' => $scOrgId],
			'sameAs' => $scBiz['same_as'],
		];
		if (!empty($scBiz['geo'])) $scLocal['geo'] = ['@type' => 'GeoCoordinates', 'latitude' => $scBiz['geo']['lat'], 'longitude' => $scBiz['geo']['lng']];
		$scGraph[] = $scLocal;
	}
	$scGraph = array_values(array_filter($scGraph, function ($n) use ($scHas) {
		if ($scHas($n['@type'])) return false;
		return !($n['@type'] === 'ProfessionalService' && $scHas('LocalBusiness'));
	}));
@endphp
@if($scGraph)
<script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@graph' => $scGraph], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endif
@endif
