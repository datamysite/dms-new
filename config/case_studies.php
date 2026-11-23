<?php

/*
|--------------------------------------------------------------------------
| Case studies shown on the redesigned (V2) pages
|--------------------------------------------------------------------------
| One list for the home page carousel, the service pages, the Case Studies page and the About page (see v2_case_studies() in
| web/home-v2/helpers.php). Order = order on the pages (global brands first).
|
|   name   brand name shown on the card / in alt text
|   cover  cover image: public/case-studies/v2/cover/{cover}.jpg  (first slide of the PDF, 1080x605)
|   pdf    PDF file name in public/case-studies/v2/ without ".pdf" (kept as delivered, spaces are URL-encoded by the helper)
|
| To add a case study: put the PDF in public/case-studies/v2/, add its cover (first slide of the PDF, 1080x605 JPEG) to
| public/case-studies/v2/cover/ and add one line here.
*/
return [
    ['name' => 'Nike',                'cover' => 'nike',          'pdf' => 'NIKE FINAL CASE STUDY'],
    ['name' => 'JD Sports',           'cover' => 'jd-sports',     'pdf' => 'JD SPORTS FINAL CASE STUDY'],
    ['name' => 'Dubai Holding',       'cover' => 'dubai-holding', 'pdf' => 'DUBAI HOLDING FINAL CASE STUDY'],
    ['name' => 'Dubai Duty Free',     'cover' => 'ddf',           'pdf' => 'DDF FINAL CASE STUDY'],
    ['name' => 'Lenskart KSA',        'cover' => 'lenskart-ksa',  'pdf' => 'LENSKART FINAL CASE STUDY'],
    ['name' => 'Lenskart UAE',        'cover' => 'lenskart-uae',  'pdf' => 'LENSKART- UAE FINAL CASE STUDY'],
    ['name' => 'Danube Properties',   'cover' => 'danube',        'pdf' => 'DANUBE FINAL CASE STUDY'],
    ['name' => 'Jetour',              'cover' => 'jetour',        'pdf' => 'JETOUR FINAL CASE STUDY'],
    ['name' => 'Géant',               'cover' => 'geant',         'pdf' => 'GEANT FINAL CASE STUDY'],
    ['name' => 'ILG Luxury Group',    'cover' => 'ilg-cerrutti',  'pdf' => 'ILG CERRUTTI FINAL CASE STUDY'],
    ['name' => 'Aswaaq',              'cover' => 'aswaaq',        'pdf' => 'ASWAAQ FINAL CASE STUDY'],
    ['name' => 'Century Financial',   'cover' => 'century',       'pdf' => 'CENTURY FINAL CASE STUDY'],
    ['name' => 'StoneX',              'cover' => 'stonex',        'pdf' => 'STONEX FINAL CASE STUDY'],
    ['name' => 'Oscar Luxury Auto',   'cover' => 'oscar',         'pdf' => 'OSCAR FINAL CASE STUDY'],
    ['name' => 'SKIN111',             'cover' => 'skin111',       'pdf' => 'SKIN111 FINAL CASE STUDY'],
    ['name' => 'Tranquil Developers', 'cover' => 'tranquil',      'pdf' => 'TRANQUIL FINAL CASE STUDY'],
];
