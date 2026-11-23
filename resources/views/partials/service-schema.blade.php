{{-- Service JSON-LD for one service page. Input: $page (a Services model, or any object with name / optional schema_name,
     title, service_type, area_served, review_count, rating_value). Business facts come from config/business.php.
     Used by web/services-v2/page.blade.php (the old services-new pages keep web/includes/elements/local-schema.blade.php). --}}
@php
    $biz = config('business');
    $root = rtrim(url('/'), '/');
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'serviceType' => $page->service_type ?? $page->name ?? null,
        'name' => $page->schema_name ?? $page->title ?? $page->name ?? $biz['name'],
        'url' => url()->current(),
        'areaServed' => $page->area_served ?? $biz['area_served'],
        'provider' => [
            '@type' => 'Organization',
            '@id' => $root . '/#organization',
            'name' => $biz['name'],
            'telephone' => $biz['phone'],
            'url' => $root . '/',
        ],
    ];

    // Only attach a rating if it's backed by a real, sourced value on the page/model.
    // Never hardcode or fabricate this — pull from wherever the reviews actually live
    // (Google Business Profile sync, Trustpilot API, a reviews table, etc).
    if (!empty($page->review_count) && !empty($page->rating_value)) {
        $schema['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => (string) $page->rating_value,
            'bestRating' => (string) ($page->best_rating ?? 5),
            'worstRating' => (string) ($page->worst_rating ?? 1),
            'reviewCount' => (string) $page->review_count,
        ];
    }
@endphp

<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
</script>
