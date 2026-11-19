<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatWidgetController extends Controller
{
    /**
     * Proxy a chat turn to Anthropic, keeping the API key server-side.
     * The widget POSTs { system, messages }; we return { reply }.
     */
    public function chat(Request $request)
    {
        $data = $request->validate([
            'system'   => 'nullable|string',
            'messages' => 'required|array',
        ]);

        $response = Http::withHeaders([
                'x-api-key'         => config('services.anthropic.key'),
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])
            ->timeout(30)
            ->post('https://api.anthropic.com/v1/messages', [
                'model'      => 'claude-sonnet-4-6',
                'max_tokens' => 1000,
                'system'     => $data['system'] ?? '',
                'messages'   => $data['messages'],
            ]);

        if ($response->failed()) {
            Log::error('anthropic_failed', ['status' => $response->status(), 'body' => $response->body()]);
            return response()->json(['error' => $response->body()], 500);
        }

        $reply = collect($response->json('content', []))
            ->where('type', 'text')
            ->pluck('text')
            ->implode("\n");

        return response()->json(['reply' => trim($reply)]);
    }

    /**
     * Record a lightweight journey event from the widget
     * (loaded / open / message / connect_shown / converted).
     */
    public function track(Request $request)
    {
        $data = $request->validate([
            'sid'          => 'required|string|max:64',
            'event'        => 'required|string|max:40',
            'meta'         => 'nullable|array',
            'messages' => 'nullable|array',
        ]);

        $allowed = ['loaded', 'auto_open', 'open', 'message', 'connect_shown', 'converted', 'teaser'];
        if (! in_array($data['event'], $allowed, true)) {
            return response()->json(['ok' => false], 422);
        }

        \App\Models\ChatEvent::create([
            'sid'          => $data['sid'],
            'event'        => $data['event'],
            'meta'         => $data['meta'] ?? null,
            'conversation' => json_encode($data['messages'], JSON_PRETTY_PRINT),
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Save a captured lead into HubSpot CRM (and optionally email it).
     */
    public function lead(Request $request)
    {
        $lead = $request->validate([
            'name'      => 'nullable|string',
            'email'     => 'required|email',
            'company'   => 'nullable|string',
            'phone'     => 'nullable|string',
            'service'   => 'nullable|string',
            'budget'    => 'nullable|string',
            'timeline'  => 'nullable|string',
            'painPoint' => 'nullable|string',
            'summary'   => 'nullable|string',
        ]);

        try {
            $this->saveToHubspot($lead);
            $this->maybeEmail($lead);
        } catch (\Throwable $e) {
            Log::error('lead_failed', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'lead_failed'], 500);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Create the contact, or update it by email if it already exists.
     * Falls back to core fields if the custom properties aren't set up yet.
     */
    private function saveToHubspot(array $lead): void
    {
        $token = config('services.hubspot.token');
        if (! $token) {
            throw new \RuntimeException('HUBSPOT_TOKEN not set');
        }

        $parts     = preg_split('/\s+/', trim($lead['name'] ?? '')) ?: [];
        $firstname = array_shift($parts) ?: '';
        $lastname  = implode(' ', $parts);

        $core = [
            'email'          => $lead['email'],
            'firstname'      => $firstname,
            'lastname'       => $lastname,
            'phone'          => $lead['phone'] ?? '',
            'company'        => $lead['company'] ?? '',
            'lifecyclestage' => 'lead',
            'hs_lead_status' => 'NEW',
        ];

        // Richer fields — only included when present.
        $custom = array_filter([
            'dms_service_interest' => $lead['service'] ?? null,
            'dms_budget_range'     => $lead['budget'] ?? null,
            'dms_timeline'         => $lead['timeline'] ?? null,
            'dms_pain_point'       => $lead['painPoint'] ?? null,
            'dms_chat_summary'     => $lead['summary'] ?? null,
        ]);

        $full = array_merge($core, $custom);

        $result = $this->upsertContact($token, $lead['email'], $full);

        // If HubSpot rejects it (commonly because the custom properties don't
        // exist yet -> 400), retry with core fields so the lead is never lost.
        if (! $result['ok'] && $result['status'] === 400 && count($custom)) {
            Log::warning('HubSpot rejected custom properties, saving core fields only', ['body' => $result['body']]);
            $result = $this->upsertContact($token, $lead['email'], $core);
        }

        if (! $result['ok']) {
            throw new \RuntimeException('hubspot_failed ' . $result['status'] . ' ' . $result['body']);
        }
    }

    private function upsertContact(string $token, string $email, array $properties): array
    {
        $base = 'https://api.hubapi.com';

        $create = Http::withToken($token)
            ->acceptJson()
            ->post("{$base}/crm/v3/objects/contacts", ['properties' => $properties]);

        if ($create->successful()) {
            return ['ok' => true, 'status' => $create->status(), 'body' => null];
        }

        // Already exists -> update by email so we don't create duplicates.
        if ($create->status() === 409) {
            $props = $properties;
            unset($props['email']); // email is the lookup key here

            $patch = Http::withToken($token)
                ->acceptJson()
                ->patch("{$base}/crm/v3/objects/contacts/" . rawurlencode($email) . '?idProperty=email', [
                    'properties' => $props,
                ]);

            return ['ok' => $patch->successful(), 'status' => $patch->status(), 'body' => $patch->body()];
        }

        return ['ok' => false, 'status' => $create->status(), 'body' => $create->body()];
    }

    /**
     * Optional email alert — no-op unless Resend env vars are configured.
     */
    private function maybeEmail(array $lead): void
    {
        $key = config('services.resend.key');
        $to  = config('services.resend.to');
        if (! $key || ! $to) {
            return;
        }

        $rows = collect([
            'Name'            => $lead['name'] ?? null,
            'Email'           => $lead['email'] ?? null,
            'Company / site'  => $lead['company'] ?? null,
            'Phone'           => $lead['phone'] ?? null,
            'Service interest'=> $lead['service'] ?? null,
            'Budget'          => $lead['budget'] ?? null,
            'Timeline'        => $lead['timeline'] ?? null,
            'Pain point'      => $lead['painPoint'] ?? null,
            'Summary'         => $lead['summary'] ?? null,
        ])->filter()
          ->map(fn ($v, $k) => '<p><b>' . e($k) . ':</b> ' . e($v) . '</p>')
          ->implode('');

        Http::withToken($key)->post('https://api.resend.com/emails', [
            'from'     => config('services.resend.from'),
            'to'       => $to,
            'reply_to' => $lead['email'],
            'subject'  => 'New lead: ' . ($lead['name'] ?? 'website visitor'),
            'html'     => '<h2>New lead from the Data My Site chat</h2>' . $rows,
        ]);
    }
}
