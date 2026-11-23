<?php

return [
    /*
    | Structured data (JSON-LD) that the Blade templates add themselves: Organization / WebSite / LocalBusiness
    | (web/home-v2/schema.blade.php), FAQPage, Service and the BreadcrumbList of the redesigned pages.
    | The database also holds hand-made schema (table snippet_code); the templates step aside for any type a snippet or the
    | page's own stored sections already provide. Set SEO_TEMPLATE_SCHEMA=false in .env to switch the template schema off entirely.
    */
    'template_schema' => env('SEO_TEMPLATE_SCHEMA', true),

    /*
    | Keep search engines out of every NON-production host (staging, preview, localhost): a robots meta tag + an X-Robots-Tag header
    | (App\Http\Middleware\StagingNoindex). OFF by default, so staging serves exactly what production serves and can be audited and
    | indexed like the live site. Set SEO_BLOCK_NON_PRODUCTION=true in that server's .env to block it again; if you do, also re-enable
    | the robots.txt rewrite in .htaccess (it is commented out there).
    */
    'block_non_production' => env('SEO_BLOCK_NON_PRODUCTION', false),
];
