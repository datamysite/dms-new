<?php

/*
|--------------------------------------------------------------------------
| Client brands ("Brands we work with")
|--------------------------------------------------------------------------
| image number => brand name, ordered by brand size (biggest first).
| Logos live in public/assets/img/clients-final/{number}.png and {number}.webp.
| Single source for the old partial (web/includes/elements/clients.blade.php)
| and the V2 pages (v2_clients() in web/home-v2/helpers.php).
*/
return [
    // Global / major brands
    38 => 'Nike',           12 => 'Intel',          35 => 'Whole Foods',    31 => 'e&',
    1  => 'Dubai Duty Free', 4 => 'DAMAC',          17 => 'MakeMyTrip',     42 => 'Dubai Holding',
    43 => 'SKIN111',        44 => 'JD Sports',
    // Large regional brands
    6  => 'Lenskart',       16 => 'Cerruti',        3  => 'Danube Properties', 2 => 'Jetour',
    39 => 'Géant',          36 => "Haldiram's",     37 => 'GMG',
    // Well-known UAE brands
    25 => 'Al Rawabi',      28 => 'Al Ain Farms',   20 => 'SIVVI',          40 => 'Aswaaq',
    7  => 'Logicom Group',  5  => 'ILG',            24 => 'MARQ Dubai',
    // Established regional businesses
    26 => 'Hayata',         27 => 'Cavo',           13 => 'Elite Group Holding', 14 => 'BNW Developments',
    8  => 'Arabian Hills',  19 => 'Tranquil Developers', 21 => 'Prime Estate',
    // Growing businesses
    18 => 'Oscar Luxury Auto', 15 => 'AYS Developers', 32 => 'USI Consultancy', 34 => 'First Super Car Rental',
    33 => 'Royalty Tourism', 29 => 'Darcey Flowers', 30 => 'Peace Homes Development',
    // Startups & boutique brands
    9  => "D's Damat",      10 => 'Metamind Inspire', 11 => 'Ask Kubeir',   22 => 'Lasa Ona',
    23 => 'Just Love UAE',  41 => 'Royal',
];
