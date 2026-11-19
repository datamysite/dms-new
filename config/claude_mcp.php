<?php

return [

    // Turn the MCP endpoint on/off without touching code.
    'enabled' => env('CLAUDE_MCP_ENABLED', true),

    // Secret Claude must present: either as "Authorization: Bearer <key>"
    // (request headers) or as the last part of the URL (/api/mcp/<key>).
    // Generate one with: php -r "echo bin2hex(random_bytes(32));"
    'access_key' => env('CLAUDE_MCP_ACCESS_KEY'),

    // How the MCP tools authenticate against your own /api/v1 routes
    // (the token your `verify.api.token` middleware accepts).
    'api_token_header' => env('CLAUDE_MCP_API_TOKEN_HEADER', 'Authorization'),
    'api_token_value' => env('CLAUDE_MCP_API_TOKEN_VALUE'),   // e.g. "Bearer abc123" or just "abc123"
    'api_prefix' => env('CLAUDE_MCP_API_PREFIX', '/api/v1'),

    // Optional: only accept connections from these IPs (comma separated). Empty = any.
    'allowed_ips' => array_filter(array_map('trim', explode(',', (string) env('CLAUDE_MCP_ALLOWED_IPS', '')))),

    'server_name' => env('CLAUDE_MCP_SERVER_NAME', 'DataMySite Website'),
];
