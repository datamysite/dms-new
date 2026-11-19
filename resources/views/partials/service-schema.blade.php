@php
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'serviceType' => $page->service_type,
        'name' => $page->schema_name ?? $page->title,
        'areaServed' => $page->area_served ?? ['Dubai', 'Abu Dhabi', 'Sharjah', 'Ajman', 'United Arab Emirates'],
        'provider' => [
            '@type' => 'LocalBusiness',
            'name' => config('app.business_name', 'DataMySite'),
            'telephone' => config('app.business_phone', '+971525287259'),
            'url' => url('/'),
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
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>