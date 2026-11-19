<?php

namespace App\Http\Controllers;

use App\Models\ChatEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatDashboardController extends Controller
{
    /** Name of the JSON column that stores the conversation. Change if yours differs. */
    private const CONV_COL = 'conversation';

    /** Maps the "stage" filter (clicked from a funnel card) to the underlying event name. */
    private const STAGE_EVENT_MAP = [
        'loaded'    => 'loaded',
        'opened'    => 'open',
        'asked'     => 'message',
        'intent'    => 'connect_shown',
        'converted' => 'converted',
    ];

    /** The dashboard page. */
    public function dashboard(Request $request)
    {
        //abort_unless($this->authorized($request), 403);

        return view('admin.claude-chat.chat-dashboard', [
            'menu' => 'claude-chat',
            'key' => (string) $request->query('key', ''),
        ]);
    }

    /** Aggregated funnel + recent activity + daily trend, polled by the dashboard. */
    public function stats(Request $request)
    {
        //abort_unless($this->authorized($request), 403);

        [$since, $until, $range] = $this->resolveRange($request);
        $stage = (string) $request->query('stage', '');

        // Distinct visitors (sid) at each stage of the journey.
        $distinct = ChatEvent::whereBetween('created_at', [$since, $until])
            ->select('event', DB::raw('COUNT(DISTINCT sid) as c'))
            ->groupBy('event')
            ->pluck('c', 'event');

        $funnel = [
            'loaded'    => (int) ($distinct['loaded'] ?? 0),
            'opened'    => (int) ($distinct['open'] ?? 0),
            'asked'     => (int) ($distinct['message'] ?? 0),
            'intent'    => (int) ($distinct['connect_shown'] ?? 0),
            'converted' => (int) ($distinct['converted'] ?? 0),
        ];

        $messagesTotal = (int) ChatEvent::whereBetween('created_at', [$since, $until])
            ->where('event', 'message')->count();

        $convRate = $funnel['asked'] > 0
            ? round($funnel['converted'] / $funnel['asked'] * 100, 1)
            : 0.0;

        // If a stage card was clicked, only show activity for visitors who reached that stage.
        $recentQuery = ChatEvent::whereBetween('created_at', [$since, $until]);
        if ($stage !== '' && isset(self::STAGE_EVENT_MAP[$stage])) {
            $stageSids = ChatEvent::whereBetween('created_at', [$since, $until])
                ->where('event', self::STAGE_EVENT_MAP[$stage])
                ->distinct()->pluck('sid');
            $recentQuery->whereIn('sid', $stageSids);
        }

        $recent = $recentQuery->latest()->limit(20)->get()
            ->map(fn ($e) => [
                'event' => $e->event,
                'via'   => $e->meta['via'] ?? ($e->meta['mode'] ?? null),
                'sid'   => substr($e->sid, 0, 6),
                'ago'   => $e->created_at->diffForHumans(null, true) . ' ago',
            ]);

        // "Today" gets an hour-by-hour trend; every other range gets a day-by-day trend.
        $granularity = $range === 'today' ? 'hour' : 'day';
        $daily = $granularity === 'hour'
            ? $this->hourlyBreakdown($since, $until)
            : $this->dailyBreakdown($since, $until);

        return response()->json([
            'range'           => $range,
            'from'            => $since->toDateString(),
            'to'              => $until->toDateString(),
            'stage'           => $stage,
            'funnel'          => $funnel,
            'messages_total'  => $messagesTotal,
            'conversion_rate' => $convRate,
            'recent'          => $recent,
            'daily'           => $daily,
            'granularity'     => $granularity,
            'generated_at'    => now()->toDateTimeString(),
        ]);
    }

    /** Latest conversation per visitor (one row per sid, most recent) — only those with at least 1 message. */
    public function conversations(Request $request)
    {
        //abort_unless($this->authorized($request), 403);

        [$since, $until, $range] = $this->resolveRange($request);
        $stage = (string) $request->query('stage', '');

        // Most recent row that has a conversation, per sid.
        $latestIds = ChatEvent::whereBetween('created_at', [$since, $until])
            ->whereNotNull(self::CONV_COL)
            ->select(DB::raw('MAX(id) as id'))
            ->groupBy('sid')
            ->pluck('id');

        $convertedSids = ChatEvent::whereBetween('created_at', [$since, $until])
            ->where('event', 'converted')
            ->distinct()->pluck('sid')->all();

        $stageSids = null;
        if ($stage !== '' && isset(self::STAGE_EVENT_MAP[$stage])) {
            $stageSids = ChatEvent::whereBetween('created_at', [$since, $until])
                ->where('event', self::STAGE_EVENT_MAP[$stage])
                ->distinct()->pluck('sid')->all();
        }

        $items = ChatEvent::whereIn('id', $latestIds)
            ->orderByDesc('id')
            ->get()
            ->filter(fn ($e) => $stageSids === null || in_array($e->sid, $stageSids, true))
            ->map(function ($e) use ($convertedSids) {
                $msgs = $this->normalizeConversation($e->{self::CONV_COL});
                $lastUser = collect($msgs)->where('role', 'user')->last();
                $preview = $lastUser['content'] ?? (end($msgs)['content'] ?? '');

                return [
                    'sid'       => substr($e->sid, 0, 6),
                    'full_sid'  => $e->sid,
                    'count'     => count($msgs),
                    'preview'   => mb_strimwidth((string) $preview, 0, 120, '…'),
                    'ago'       => $e->created_at->diffForHumans(null, true) . ' ago',
                    'converted' => in_array($e->sid, $convertedSids, true),
                ];
            })
            // Only visitors who actually exchanged at least one real message.
            ->filter(fn ($item) => $item['count'] >= 1)
            ->values();

        return response()->json([
            'range' => $range,
            'stage' => $stage,
            'items' => $items,
        ]);
    }

    /** The full transcript for a single visitor's latest conversation. */
    public function conversation(Request $request)
    {
        //abort_unless($this->authorized($request), 403);

        $sid = (string) $request->query('sid', '');
        if ($sid === '') {
            return response()->json(['messages' => []]);
        }

        $row = ChatEvent::where('sid', $sid)
            ->whereNotNull(self::CONV_COL)
            ->orderByDesc('id')
            ->first();

        return response()->json([
            'sid'      => substr($sid, 0, 6),
            'messages' => $row ? $this->normalizeConversation($row->{self::CONV_COL}) : [],
        ]);
    }

    /**
     * Resolve the requested range into a concrete [since, until, label] window.
     * Supports the built-in ranges (today / 7d / 30d) plus a custom ?range=custom&from=YYYY-MM-DD&to=YYYY-MM-DD.
     */
    private function resolveRange(Request $request): array
    {
        $range = (string) $request->query('range', '7d');

        if ($range === 'custom') {
            $from = $request->query('from');
            $to   = $request->query('to');

            try {
                $since = $from ? Carbon::parse($from)->startOfDay() : Carbon::now()->subDays(7)->startOfDay();
            } catch (\Throwable $e) {
                $since = Carbon::now()->subDays(7)->startOfDay();
            }

            try {
                $until = $to ? Carbon::parse($to)->endOfDay() : Carbon::now();
            } catch (\Throwable $e) {
                $until = Carbon::now();
            }

            if ($since->greaterThan($until)) {
                [$since, $until] = [$until->copy()->startOfDay(), $since->copy()->endOfDay()];
            }

            return [$since, $until, 'custom'];
        }

        $until = Carbon::now();
        $since = match ($range) {
            'today' => Carbon::today(),
            '30d'   => Carbon::now()->subDays(30),
            default => Carbon::now()->subDays(7),
        };

        return [$since, $until, $range];
    }

    /** Per-day distinct-visitor counts for each funnel stage, used to draw the trend chart (gap-filled). */
    private function dailyBreakdown(Carbon $since, Carbon $until): array
    {
        $rows = ChatEvent::whereBetween('created_at', [$since, $until])
            ->select(DB::raw('DATE(created_at) as d'), 'event', DB::raw('COUNT(DISTINCT sid) as c'))
            ->groupBy('d', 'event')
            ->orderBy('d')
            ->get();

        $byBucket = $this->groupRowsByBucket($rows);

        $daily = [];
        $cursor = $since->copy()->startOfDay();
        $end    = $until->copy()->startOfDay();
        while ($cursor->lessThanOrEqualTo($end)) {
            $key = $cursor->toDateString();
            $daily[] = array_merge(['date' => $key], $byBucket[$key] ?? $this->emptyStageCounts());
            $cursor->addDay();
        }

        return $daily;
    }

    /** Per-hour distinct-visitor counts for each funnel stage — used when range=today (gap-filled). */
    private function hourlyBreakdown(Carbon $since, Carbon $until): array
    {
        $rows = ChatEvent::whereBetween('created_at', [$since, $until])
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') as d"), 'event', DB::raw('COUNT(DISTINCT sid) as c'))
            ->groupBy('d', 'event')
            ->orderBy('d')
            ->get();

        $byBucket = $this->groupRowsByBucket($rows);

        $hourly = [];
        $cursor = $since->copy()->startOfHour();
        $end    = $until->copy()->startOfHour();
        while ($cursor->lessThanOrEqualTo($end)) {
            $key = $cursor->format('Y-m-d H:00:00');
            $hourly[] = array_merge(['date' => $key], $byBucket[$key] ?? $this->emptyStageCounts());
            $cursor->addHour();
        }

        return $hourly;
    }

    /** Turns raw grouped rows (by bucket + event) into [bucket => stage-count-array]. */
    private function groupRowsByBucket($rows): array
    {
        $map = [];
        foreach ($rows as $r) {
            $map[$r->d] = $map[$r->d] ?? $this->emptyStageCounts();

            $key = match ($r->event) {
                'loaded'        => 'loaded',
                'open'          => 'opened',
                'message'       => 'asked',
                'connect_shown' => 'intent',
                'converted'     => 'converted',
                default         => null,
            };

            if ($key !== null) {
                $map[$r->d][$key] = (int) $r->c;
            }
        }

        return $map;
    }

    private function emptyStageCounts(): array
    {
        return ['loaded' => 0, 'opened' => 0, 'asked' => 0, 'intent' => 0, 'converted' => 0];
    }

    /** Turn the stored JSON into a clean [{role, content}] list for display. */
    private function normalizeConversation($raw): array
    {
        $data = is_array($raw) ? $raw : json_decode((string) $raw, true);
        if (! is_array($data)) {
            return [];
        }

        $out = [];
        foreach ($data as $m) {
            if (! is_array($m)) {
                continue;
            }

            $role = $m['role'] ?? $m['from'] ?? 'user';
            $role = $role === 'assistant' ? 'assistant' : 'user';

            $content = $m['content'] ?? $m['text'] ?? $m['message'] ?? '';
            if (is_array($content)) { // Anthropic-style content blocks
                $content = collect($content)
                    ->map(fn ($b) => is_array($b) ? ($b['text'] ?? '') : (string) $b)
                    ->implode("\n");
            }
            $content = (string) $content;

            // Strip internal tokens / CRM JSON the bot emits after them.
            foreach (['<<<CONNECT>>>', '<<<LEAD_FORM>>>'] as $tok) {
                $pos = strpos($content, $tok);
                if ($pos !== false) {
                    $content = substr($content, 0, $pos);
                }
            }

            $content = trim($content);
            if ($content === '' || str_starts_with($content, '[Visitor submitted')) {
                continue; // skip empty or internal system notes
            }

            $out[] = ['role' => $role, 'content' => $content];
        }

        return $out;
    }
}