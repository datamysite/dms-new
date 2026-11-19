<?php

namespace App\Http\Controllers\Mcp;

use App\Http\Controllers\Controller;
use App\Mcp\WebsiteTools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Minimal remote MCP server (Streamable HTTP transport, stateless JSON responses).
 * Works on Laravel 8+ / PHP 7.4+ with no extra packages.
 *
 * Claude connects to POST /api/mcp (or /api/mcp/<access key>).
 */
class McpServerController extends Controller
{
    const SUPPORTED_VERSIONS = ['2025-11-25', '2025-06-18', '2025-03-26', '2024-11-05'];

    protected $tools;

    public function __construct(WebsiteTools $tools)
    {
        $this->tools = $tools;
    }

    public function handle(Request $request)
    {
        // This server doesn't push server-initiated messages, so there is no SSE stream.
        if ($request->isMethod('GET')) {
            return response('', 405)->header('Allow', 'POST, DELETE');
        }
        if ($request->isMethod('DELETE')) {
            return response('', 204);   // stateless: nothing to close
        }

        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return $this->json($this->error(null, -32700, 'Parse error'));
        }

        // A JSON-RPC batch (older protocol versions) or a single message.
        $isBatch = array_keys($payload) === range(0, count($payload) - 1);
        $messages = $isBatch ? $payload : [$payload];

        $responses = [];
        foreach ($messages as $message) {
            $result = $this->dispatch(is_array($message) ? $message : []);
            if ($result !== null) {
                $responses[] = $result;
            }
        }

        if (!$responses) {
            return response('', 202);   // only notifications were sent
        }

        return $this->json($isBatch ? $responses : $responses[0]);
    }

    protected function dispatch(array $msg)
    {
        $id = array_key_exists('id', $msg) ? $msg['id'] : null;
        $method = isset($msg['method']) ? $msg['method'] : null;
        $params = isset($msg['params']) && is_array($msg['params']) ? $msg['params'] : [];
        $isNotification = !array_key_exists('id', $msg);

        if (!$method) {
            return $isNotification ? null : $this->error($id, -32600, 'Invalid Request');
        }

        if ($isNotification) {
            return null;   // notifications/initialized, notifications/cancelled, ...
        }

        try {
            switch ($method) {
                case 'initialize':
                    return $this->result($id, $this->initialize($params));
                case 'ping':
                    return $this->result($id, new \stdClass());
                case 'tools/list':
                    return $this->result($id, ['tools' => $this->tools->definitions()]);
                case 'tools/call':
                    return $this->result($id, $this->callTool($params));
                case 'resources/list':
                    return $this->result($id, ['resources' => []]);
                case 'resources/templates/list':
                    return $this->result($id, ['resourceTemplates' => []]);
                case 'prompts/list':
                    return $this->result($id, ['prompts' => []]);
                default:
                    return $this->error($id, -32601, "Method not found: $method");
            }
        } catch (Throwable $e) {
            Log::error('MCP error', ['method' => $method, 'error' => $e->getMessage()]);

            return $this->error($id, -32603, 'Internal error: ' . $e->getMessage());
        }
    }

    protected function initialize(array $params)
    {
        $requested = isset($params['protocolVersion']) ? $params['protocolVersion'] : null;
        $version = in_array($requested, self::SUPPORTED_VERSIONS, true) ? $requested : self::SUPPORTED_VERSIONS[0];

        return [
            'protocolVersion' => $version,
            'capabilities' => ['tools' => ['listChanged' => false]],
            'serverInfo' => [
                'name' => config('claude_mcp.server_name', 'Website'),
                'version' => '1.0.0',
            ],
            'instructions' => implode("\n", [
                'Tools for managing the ' . config('app.url') . ' website: meta tags + H1 per URL, service page sections, and blog posts.',
                '- Before changing meta tags, call get_meta_tags for the URL so you can show the user the current values.',
                '- Keep meta titles around 50-60 characters and descriptions around 140-160 characters; report any warnings returned.',
                '- Prefer bulk_upsert_meta_tags when changing many pages.',
                '- Service pages are built from sections (table service_sections). Find a page with list_services, see its sections with get_service_sections, then read one with get_service_section before editing it.',
                '- Section content is Blade that runs on the live site: keep {{ }} expressions, @include, @foreach/@endforeach, @csrf and the existing HTML classes intact. For small text changes use update_service_section with find/replace.',
                '- To hide a section use is_active=false rather than deleting it. Every change is saved as a revision and can be restored.',
                '- Create blog posts as drafts. Only call publish_blog or delete_meta_tags when the user explicitly asked for it.',
            ]),
        ];
    }

    protected function callTool(array $params)
    {
        $name = isset($params['name']) ? (string) $params['name'] : '';
        $args = isset($params['arguments']) && is_array($params['arguments']) ? $params['arguments'] : [];

        $known = array_column($this->tools->definitions(), 'name');
        if (!in_array($name, $known, true)) {
            return $this->toolText("Unknown tool: $name", true);
        }

        // Required arguments
        foreach ($this->tools->definitions() as $def) {
            if ($def['name'] === $name && !empty($def['inputSchema']['required'])) {
                foreach ($def['inputSchema']['required'] as $field) {
                    if (!array_key_exists($field, $args) || $args[$field] === '' || $args[$field] === null) {
                        return $this->toolText("Missing required argument: $field", true);
                    }
                }
            }
        }

        $result = $this->tools->call($name, $args);
        $ok = $result['status'] >= 200 && $result['status'] < 300;

        Log::info('MCP tool call', [
            'tool' => $name,
            'status' => $result['status'],
            'args' => mb_substr(json_encode($args, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 0, 500),
        ]);

        $text = json_encode(
            ['http_status' => $result['status'], 'response' => $result['body']],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        if (mb_strlen($text) > 60000) {
            $text = mb_substr($text, 0, 60000) . "\n… (truncated — use filters or pagination)";
        }

        return $this->toolText($text, !$ok);
    }

    protected function toolText($text, $isError = false)
    {
        return ['content' => [['type' => 'text', 'text' => $text]], 'isError' => (bool) $isError];
    }

    protected function result($id, $result)
    {
        return ['jsonrpc' => '2.0', 'id' => $id, 'result' => $result];
    }

    protected function error($id, $code, $message)
    {
        return ['jsonrpc' => '2.0', 'id' => $id, 'error' => ['code' => $code, 'message' => $message]];
    }

    protected function json($data)
    {
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
