<?php
// File: config/redirects.php
// Settings for database-driven 301 redirects (HandleDbRedirects middleware + RedirectApiController).

return [

    // Master switch. Set REDIRECTS_ENABLED=false in .env to turn the middleware off instantly
    // (then the old routes/redirects.php, if still required, takes over again).
    'enabled' => env('REDIRECTS_ENABLED', true),

    // Old URLs are matched exactly as typed, like Laravel routes are today.
    // '/blogs/whatsApp-and-messaging-marketing' depends on this staying true.
    'case_sensitive' => true,

    // Keep ?utm_source=... etc. when redirecting.
    'preserve_query' => true,

    // Count hits per redirect (written after the response is sent).
    'track_hits' => true,

    // Cache key for the redirect map (cleared automatically on every change).
    'cache_key' => 'redirects.map.v1',

    // Requests under these prefixes are never redirected, and no redirect may be created from them.
    'reserved_prefixes' => [
        '/api', '/admin', '/login', '/logout', '/storage', '/public', '/vendor',
        '/build', '/livewire', '/_debugbar', '/sanctum', '/up',
    ],

    // Lock-List / protected URLs: the API refuses to redirect these. Edit this file to change it;
    // there is deliberately no API flag to override it.
    // Exact paths, or a prefix ending in * (e.g. '/outdoor-advertising/*').
    'protected' => [
        '/',
        '/blogs',
        // Add the Lock-List URLs here, one per line, e.g.:
        // '/public-relations-pr-coverage',
        // '/how-much-does-a-pr-agency-cost-in-dubai-in-2026-full-guide',
    ],

    // Targets that act as a soft 404 (Google treats a redirect to them as "page deleted").
    // The API refuses them unless allow_soft404=true is sent.
    'soft_404_targets' => ['/', '/blogs'],

    // Allowed status codes.
    'status_codes' => [301, 302, 307, 308],

    // Allow redirects to other domains (https only).
    'allow_external_targets' => false,

    // Before saving, request the target URL from this server and require a 200
    // (no 404, no further redirect). Set REDIRECTS_VERIFY_TARGETS=false if the server
    // cannot call its own domain (some firewalls block it).
    'verify_targets' => env('REDIRECTS_VERIFY_TARGETS', true),

    // Timeout in seconds for target checks and the /redirects-check endpoint.
    'http_timeout' => 8,

    // Max hops followed by /redirects-check.
    'max_hops' => 10,
];
