<?php

/*
|--------------------------------------------------------------------------
| Business details: single source for the redesigned (V2) pages
|--------------------------------------------------------------------------
| Used by the structured data (web/home-v2/schema.blade.php) and by the V2 header, footer, popups, contact / about pages through the
| helpers in web/home-v2/helpers.php (v2_biz(), v2_wa_url(), v2_tel()). Values are copied from the live site. Only add facts that are
| true and shown on the site: no invented ratings, review counts, prices or coordinates.
| To add later when confirmed: 'geo' => ['lat' => ..., 'lng' => ...] and a Facebook page URL in 'social'.
*/
$social = [
    ['label' => 'LinkedIn',  'icon' => 'bi-linkedin',  'url' => 'https://www.linkedin.com/company/datamysite/'],
    ['label' => 'Instagram', 'icon' => 'bi-instagram', 'url' => 'https://www.instagram.com/datamysite/'],
    ['label' => 'Pinterest', 'icon' => 'bi-pinterest', 'url' => 'https://www.pinterest.com/DataMySiteDubai/_profile/'],
    ['label' => 'X',         'icon' => 'bi-twitter-x', 'url' => 'https://x.com/DataMySite'],
    ['label' => 'YouTube',   'icon' => 'bi-youtube',   'url' => 'https://www.youtube.com/@DataMySiteMarketing'],
];

return [
    'name'        => 'DataMySite',
    'legal_name'  => 'DataMySite Marketing',
    'description' => 'DataMySite is a Dubai marketing and advertising agency: outdoor, digital and transit media, influencer and celebrity marketing, SEO, PR and lead generation.',
    'email'       => 'contact@datamysite.com',

    // mobile / WhatsApp
    'phone'         => '+971525287259',      // E.164, for tel: links and structured data
    'phone_display' => '+971 52 528 7259',   // as shown to visitors
    'whatsapp'      => '971525287259',       // digits only, for api.whatsapp.com / wa.me links

    // office landline (042 957 001)
    'telephone'         => '+97142957001',
    'telephone_display' => '042 957 001',

    'logo'        => 'public/assets/img/logo.png',   // path under the site root, turned into a full URL with url()
    'address'     => [
        'street'   => 'Office 205, Al Bayan Building, Dubai Investment Park 1',
        'locality' => 'Dubai',
        'country'  => 'AE',
    ],
    'address_line' => 'Office 205, Al Bayan Building, Dubai Investment Park 1, Dubai - UAE',   // as shown to visitors

    // Mon-Fri 9:30 am - 7:30 pm, Saturday & Sunday closed
    'hours'       => [
        ['days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'], 'opens' => '09:30', 'closes' => '19:30'],
    ],
    'hours_text'  => ['Mon - Fri | 9:30 am - 7:30 pm', 'Saturday & Sunday | Closed'],

    'area_served' => ['Dubai', 'Abu Dhabi', 'Sharjah', 'Ajman', 'United Arab Emirates'],

    // social profiles shown in the footer; the URLs also feed `sameAs` in the structured data
    'social'      => $social,
    'same_as'     => array_column($social, 'url'),

    // Google Business Profile reviews (fallback numbers while the live Google data is not cached yet)
    'reviews' => [
        'url'            => 'https://www.google.com/maps/place/DMS+-+DataMySite+Marketing/@24.9978253,55.1727716,936m/data=!3m1!1e3!4m8!3m7!1s0x3e5f7347c3a1a1d1:0xad7a49fd99d3061!8m2!3d24.9978253!4d55.1727716!9m1!1b1!16s%2Fg%2F11ybf42kl_?entry=ttu&g_ep=EgoyMDI1MTIwOS4wIKXMDSoASAFQAw%3D%3D',
        'fallback_score' => 5.0,
        'fallback_total' => 46,
    ],
];
