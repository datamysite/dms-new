<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Lets Claude in when it presents the MCP access key:
 *  - header:  Authorization: Bearer <key>   (Claude "Request headers")
 *  - or URL:  /api/mcp/<key>                (when request headers aren't available)
 */
class VerifyMcpAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!config('claude_mcp.enabled')) {
            abort(404);
        }

        $key = (string) config('claude_mcp.access_key');
        if (strlen($key) < 32) {
            return $this->deny('MCP access key is not configured (CLAUDE_MCP_ACCESS_KEY, min 32 chars).', 503);
        }

        $allowed = (array) config('claude_mcp.allowed_ips', []);
        if ($allowed && !in_array($request->ip(), $allowed, true)) {
            return $this->deny('Forbidden.', 403);
        }

        $fromHeader = (string) $request->bearerToken();
        $fromPath = (string) $request->route('key');

        $ok = ($fromHeader !== '' && hash_equals($key, $fromHeader))
            || ($fromPath !== '' && hash_equals($key, $fromPath));

        if (!$ok) {
            return $this->deny('Unauthorized.', 401);
        }

        return $next($request);
    }

    protected function deny($message, $status)
    {
        return response()->json([
            'jsonrpc' => '2.0',
            'id' => null,
            'error' => ['code' => -32001, 'message' => $message],
        ], $status);
    }
}
