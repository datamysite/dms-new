<?php

return [
    /*
    | Structured data (JSON-LD) that the Blade templates add themselves: Organization / WebSite / LocalBusiness
    | (web/home-v2/schema.blade.php), FAQPage, Service and the BreadcrumbList of the redesigned pages.
    | The database also holds hand-made schema (table snippet_code); the templates step aside for any type a snippet or the
    | page's own stored sections already provide. Set SEO_TEMPLATE_SCHEMA=false in .env to switch the template schema off entirely.
    */
    'template_schema' => env('SEO_TEMPLATE_SCHEMA', true),
];
