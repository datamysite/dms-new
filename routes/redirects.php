<?php
// File: routes/redirects.php
// Redirects now live in the database (table `redirects`) and are served by the
// HandleDbRedirects middleware. Manage them through the API, not this file.
//
// This file is kept only as an emergency fallback. If you ever need it:
//   1. set REDIRECTS_ENABLED=false in .env
//   2. restore the old list from routes/redirects.legacy.php into $redirects below
//   3. php artisan optimize:clear
//
// While REDIRECTS_ENABLED is true, leave $redirects empty: the database is the single source.

use Illuminate\Support\Facades\Route;

$redirects = [
    // intentionally empty
];

foreach ($redirects as $from => $to) {
    Route::permanentRedirect($from, $to); // 301
}
