<?php

/*
|--------------------------------------------------------------------------
| DMS Admin API
|--------------------------------------------------------------------------
| Extra endpoints so the DMS - Connect agent can do the work that used to
| need a developer: template edits, cache purge, IndexNow, sitemap,
| file-based redirects, duplicate meta rows and live-page inspection.
| Every write keeps a revision and can be restored.
*/

return [

    // Bearer token the agent sends. Set DMS_ADMIN_API_TOKEN in .env (40+ random chars).
    'token' => env('DMS_ADMIN_API_TOKEN'),

    // Turn every write endpoint off in one switch (reads keep working).
    'writes_enabled' => env('DMS_ADMIN_WRITES', true),

    // Public site URL used for page inspection, IndexNow and the sitemap.
    'site_url' => rtrim(env('DMS_SITE_URL', env('APP_URL', 'https://datamysite.com')), '/'),

    /*
    | Template files the agent may read and edit, relative to resources/views.
    | Only these files - nothing else under resources/ can be touched.
    | Add or remove entries to fit the real layout of the site.
    */
    'templates' => [
        'web/includes/footer.blade.php',
        'web/includes/header.blade.php',
        'web/includes/elements/local-schema.blade.php',
        'web/includes/elements/clients.blade.php',
        'web/includes/elements/case-studies.blade.php',
        'web/includes/elements/contact.blade.php',
        'web/blogs/detail.blade.php',
        'web/blogs/index.blade.php',
        'web/blogs/category.blade.php',
        'web/services/detail.blade.php',
        'web/services/subservice.blade.php',
        'web/home.blade.php',
        'web/layouts/app.blade.php',
    ],

    // Also allow any file under these folders (relative to resources/views). Keep it short.
    'template_dirs' => [
        'web/includes',
        'web/partials',
        'web/blogs',
        'web/services-new'
    ],

    // Largest template the API accepts on write, in bytes.
    'template_max_bytes' => 400000,

    // File with Route::redirect(...) lines (TT-02 broken redirects live here).
    'redirects_file' => base_path('routes/redirects.php'),

    // Table names used by the existing CMS.
    'tables' => [
        'blogs' => 'blogs',
        'meta_tags' => env('DMS_META_TABLE', 'meta_tags'),
        'redirects' => 'redirects',
    ],

    // IndexNow (Bing, Yandex, Seznam). Key file must exist at public/{key}.txt.
    'indexnow' => [
        'key' => env('INDEXNOW_KEY'),
        'endpoint' => 'https://api.indexnow.org/indexnow',
        'auto_on_publish' => env('INDEXNOW_AUTO', true),
    ],

    // LiteSpeed purge on save/publish.
    'litespeed' => [
        'auto_on_publish' => env('LSCACHE_AUTO_PURGE', true),
    ],

    // Sitemap output.
    'sitemap' => [
        'path' => base_path('sitemap.xml'),
        // Static service/hub URLs come from this table (path column) when it exists.
        'services_table' => 'services',
        // Extra fixed URLs always included.
        'extra_paths' => ['/', '/blogs', '/about-us', '/contact-us', '/faq'],
        // Paths never included (noindex pages etc.).
        'exclude_paths' => ['/seo-services-dubai', '/profile'],

           'auto_on_publish' => env('SITEMAP_AUTO', true),
           'update_route' => '/update-sitemap',
    ],
];
