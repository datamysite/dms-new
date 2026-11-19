<?php

namespace App\Mcp;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

/**
 * Calls your own /api/v1 routes from inside the app (no network hop), so the
 * MCP tools reuse exactly the same controllers, validation and middleware
 * as the REST API.
 */
class ApiBridge
{
    /**
     * @return array{status:int, body:mixed}
     */
    public function call($method, $path, array $data = [])
    {
        $method = strtoupper($method);
        $uri = rtrim(config('claude_mcp.api_prefix', '/api/v1'), '/') . '/' . ltrim($path, '/');

        $server = [
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
            'REMOTE_ADDR' => '127.0.0.1',
        ];
        $header = (string) config('claude_mcp.api_token_header', 'Authorization');
        $value = (string) config('claude_mcp.api_token_value');
        if ($value !== '') {
            $server['HTTP_' . strtoupper(str_replace('-', '_', $header))] = $value;
        }

        $app = app();
        $original = $app->bound('request') ? $app->make('request') : null;

        // Keep the real scheme + host so url() inside the API behaves exactly as for outside calls.
        $root = $original ? $original->getSchemeAndHttpHost() : rtrim((string) config('app.url'), '/');

        $isRead = in_array($method, ['GET', 'HEAD', 'DELETE'], true);
        $sub = Request::create(
            $root . $uri,
            $method,
            $isRead ? $data : [],
            [],
            [],
            $server,
            $isRead ? null : json_encode($data)
        );

        try {
            $response = $app->make(Kernel::class)->handle($sub);
        } finally {
            if ($original) {
                $app->instance('request', $original);
                Facade::clearResolvedInstance('request');
            }
        }

        $content = $response->getContent();
        $decoded = json_decode((string) $content, true);

        return [
            'status' => $response->getStatusCode(),
            'body' => json_last_error() === JSON_ERROR_NONE ? $decoded : mb_substr((string) $content, 0, 2000),
        ];
    }
}
