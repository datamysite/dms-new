<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceSection;
use App\Models\ServiceSectionRevision;
use App\Models\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Manage the service_sections table (Blade content of each service page, per section).
 * {service} accepts a services.id, a slug ("pink-taxi") or a page URL/path
 * ("/transit-media/taxi-advertising/pink-taxi").
 */
class ServiceSectionApiController extends Controller
{
    const AREAS = ['head', 'main', 'after_main', 'scripts'];
    const TYPES = ['style', 'hero', 'content', 'include', 'subservices', 'blogs', 'faq', 'script'];

    // ------------------------------------------------------------- services

    /** GET /v1/services?search=&parent_id= */
    public function services(Request $request)
    {
        $all = Services::query()->orderBy('parent_id')->orderBy('id')->get(['id', 'name', 'slug', 'parent_id']);
        $byId = $all->keyBy('id');
        $counts = ServiceSection::select('service_id', DB::raw('COUNT(*) as total'))->groupBy('service_id')->pluck('total', 'service_id');

        $rows = $all->map(function ($s) use ($byId, $counts) {
            return [
                'id' => $s->id,
                'name' => trim($s->name),
                'slug' => $s->slug,
                'parent_id' => (int) $s->parent_id,
                'path' => '/' . $this->pathFor($s, $byId),
                'sections' => (int) ($counts[$s->id] ?? 0),
            ];
        });

        if ($search = $request->query('search')) {
            $needle = mb_strtolower($search);
            $rows = $rows->filter(function ($r) use ($needle) {
                return mb_strpos(mb_strtolower($r['name'] . ' ' . $r['path']), $needle) !== false;
            });
        }
        if ($request->filled('parent_id')) {
            $parent = (int) $request->query('parent_id');
            $rows = $rows->where('parent_id', $parent);
        }

        return response()->json(['data' => $rows->sortBy('path')->values()]);
    }

    /** GET /v1/services/{service}/sections?with_content=1 */
    public function index(Request $request, $service)
    {
        $page = $this->resolveService($service);
        $withContent = $request->boolean('with_content');

        $sections = ServiceSection::where('service_id', $page->id)->orderBy('sort_order')->orderBy('id')->get();

        return response()->json([
            'service' => $this->serviceInfo($page),
            'data' => $sections->map(function ($s) use ($withContent) {
                return $this->format($s, $withContent);
            })->values(),
        ]);
    }

    /** POST /v1/services/{service}/sections */
    public function store(Request $request, $service)
    {
        $page = $this->resolveService($service);
        $data = $request->validate([
            'section_key' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9][a-z0-9\-]*$/'],
            'title' => 'nullable|string|max:255',
            'content' => 'required|string|max:200000',
            'area' => ['sometimes', Rule::in(self::AREAS)],
            'type' => ['sometimes', Rule::in(self::TYPES)],
            'sort_order' => 'sometimes|integer|min:0',
            'after_section_key' => 'sometimes|string|max:100',
            'is_active' => 'sometimes|boolean',
        ]);

        if (ServiceSection::where('service_id', $page->id)->where('section_key', $data['section_key'])->exists()) {
            throw ValidationException::withMessages(['section_key' => 'This service already has a section with that key.']);
        }
        $this->guardBlade($data['content']);

        $area = $data['area'] ?? 'main';
        $sort = $data['sort_order'] ?? null;
        if ($sort === null && !empty($data['after_section_key'])) {
            $after = ServiceSection::where('service_id', $page->id)->where('section_key', $data['after_section_key'])->first();
            if (!$after) {
                throw ValidationException::withMessages(['after_section_key' => 'No section with that key on this service.']);
            }
            $area = $data['area'] ?? $after->area;
            $sort = $this->sortAfter($page->id, $after);
        }
        if ($sort === null) {
            $sort = (int) ServiceSection::where('service_id', $page->id)->where('area', $area)->max('sort_order') + 10;
        }

        $section = ServiceSection::create([
            'service_id' => $page->id,
            'area' => $area,
            'section_key' => $data['section_key'],
            'type' => $data['type'] ?? 'content',
            'title' => $data['title'] ?? null,
            'content' => $data['content'],
            'sort_order' => $sort,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return response()->json(['data' => $this->format($section, true), 'url' => url($this->serviceInfo($page)['path'])], 201);
    }

    /** POST /v1/services/{service}/sections/reorder  {order: [sectionId, ...]} (main area order) */
    public function reorder(Request $request, $service)
    {
        $page = $this->resolveService($service);
        $order = $request->validate(['order' => 'required|array|min:1', 'order.*' => 'integer'])['order'];

        $sections = ServiceSection::where('service_id', $page->id)->whereIn('id', $order)->get()->keyBy('id');
        if ($sections->count() !== count(array_unique($order))) {
            throw ValidationException::withMessages(['order' => 'Every id must be a section of this service, listed once.']);
        }

        DB::transaction(function () use ($order, $sections) {
            foreach (array_values($order) as $i => $id) {
                $s = $sections[$id];
                $this->revision($s, 'reorder');
                $s->sort_order = ($i + 1) * 10;
                $s->save();
            }
        });

        return $this->index($request, $page->id);
    }

    // ------------------------------------------------------------- sections

    /** GET /v1/service-sections/{id} */
    public function show($id)
    {
        $section = ServiceSection::findOrFail($id);

        return response()->json(['data' => $this->format($section, true), 'service' => $this->serviceInfo(Services::find($section->service_id))]);
    }

    /** PATCH /v1/service-sections/{id} — only the fields you send change */
    public function update(Request $request, $id)
    {
        $section = ServiceSection::findOrFail($id);
        $data = $request->validate([
            'content' => 'sometimes|required|string|max:200000',
            'title' => 'sometimes|nullable|string|max:255',
            'section_key' => ['sometimes', 'string', 'max:100', 'regex:/^[a-z0-9][a-z0-9\-]*$/'],
            'area' => ['sometimes', Rule::in(self::AREAS)],
            'type' => ['sometimes', Rule::in(self::TYPES)],
            'sort_order' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'find' => 'sometimes|string|max:20000',
            'replace' => 'required_with:find|nullable|string|max:20000',
        ]);

        // Small edits without resending the whole section: {"find": "...", "replace": "..."}
        if (isset($data['find'])) {
            $count = substr_count($section->content, $data['find']);
            if ($count !== 1) {
                throw ValidationException::withMessages(['find' => "The text to find must occur exactly once in the section (found $count times)."]);
            }
            $data['content'] = str_replace($data['find'], (string) $data['replace'], $section->content);
        }
        unset($data['find'], $data['replace']);

        if (isset($data['section_key']) && $data['section_key'] !== $section->section_key
            && ServiceSection::where('service_id', $section->service_id)->where('section_key', $data['section_key'])->exists()) {
            throw ValidationException::withMessages(['section_key' => 'This service already has a section with that key.']);
        }
        if (isset($data['content'])) {
            $this->guardBlade($data['content'], $section->content);
        }

        $before = $this->format($section, true);
        DB::transaction(function () use ($section, $data) {
            $this->revision($section, 'update');
            $section->fill($data)->save();
        });

        return response()->json(['data' => $this->format($section->fresh(), true), 'previous' => $before]);
    }

    /** DELETE /v1/service-sections/{id} */
    public function destroy($id)
    {
        $section = ServiceSection::findOrFail($id);
        DB::transaction(function () use ($section) {
            $this->revision($section, 'delete');
            $section->delete();
        });

        return response()->json(['message' => 'Section deleted. It can be restored from its revision.', 'deleted' => $this->format($section, false)]);
    }

    /** GET /v1/service-sections/{id}/revisions */
    public function revisions($id)
    {
        $items = ServiceSectionRevision::where('section_id', $id)->orderByDesc('id')->limit(50)->get();

        return response()->json(['data' => $items->map(function ($r) {
            return [
                'id' => $r->id,
                'action' => $r->action,
                'created_at' => optional($r->created_at)->toIso8601String(),
                'title' => $r->snapshot['title'] ?? null,
                'content_length' => mb_strlen($r->snapshot['content'] ?? ''),
            ];
        })]);
    }

    /** GET /v1/services/{service}/deleted-sections */
    public function deleted($service)
    {
        $page = $this->resolveService($service);
        $items = ServiceSectionRevision::where('service_id', $page->id)->where('action', 'delete')->orderByDesc('id')->limit(50)->get();

        return response()->json(['data' => $items->map(function ($r) {
            return ['revision_id' => $r->id, 'section_key' => $r->snapshot['section_key'] ?? null, 'title' => $r->snapshot['title'] ?? null, 'deleted_at' => optional($r->created_at)->toIso8601String()];
        })]);
    }

    /** POST /v1/service-section-revisions/{revision}/restore */
    public function restore($revisionId)
    {
        $revision = ServiceSectionRevision::findOrFail($revisionId);
        $snap = $revision->snapshot;

        $section = DB::transaction(function () use ($revision, $snap) {
            $section = $revision->section_id ? ServiceSection::find($revision->section_id) : null;
            if ($section) {
                $this->revision($section, 'restore');
            } else {
                // Bring a deleted section back under its original id.
                $section = new ServiceSection(['service_id' => $revision->service_id]);
                if ($revision->section_id) {
                    $section->id = $revision->section_id;
                }
                if (ServiceSection::where('service_id', $revision->service_id)->where('section_key', $snap['section_key'])->exists()) {
                    $snap['section_key'] .= '-restored-' . $revision->id;
                }
            }
            $section->fill(array_intersect_key($snap, array_flip(['area', 'section_key', 'type', 'title', 'content', 'sort_order', 'is_active'])));
            $section->save();

            return $section;
        });

        return response()->json(['data' => $this->format($section->fresh(), true)]);
    }

    // -------------------------------------------------------------- helpers

    protected function resolveService($value)
    {
        $value = trim(rawurldecode((string) $value));
        if (ctype_digit($value)) {
            $page = Services::find((int) $value);
        } else {
            $path = trim((string) (parse_url($value, PHP_URL_PATH) ?: $value), '/');
            $segments = array_values(array_filter(explode('/', $path)));
            $page = $segments ? Services::where('slug', end($segments))->first() : null;
        }
        if (!$page) {
            abort(response()->json(['message' => "No service found for '$value'. Use list_services to find it."], 404));
        }

        return $page;
    }

    protected function pathFor($service, $byId)
    {
        $parts = [$service->slug];
        $guard = 0;
        $parent = (int) $service->parent_id;
        while ($parent && isset($byId[$parent]) && $guard++ < 10) {
            array_unshift($parts, $byId[$parent]->slug);
            $parent = (int) $byId[$parent]->parent_id;
        }

        return implode('/', $parts);
    }

    protected function serviceInfo($page)
    {
        if (!$page) {
            return null;
        }
        $byId = Services::get(['id', 'slug', 'parent_id'])->keyBy('id');

        return ['id' => $page->id, 'name' => trim($page->name), 'slug' => $page->slug, 'path' => '/' . $this->pathFor($page, $byId)];
    }

    protected function sortAfter($serviceId, ServiceSection $after)
    {
        $next = ServiceSection::where('service_id', $serviceId)->where('area', $after->area)
            ->where('sort_order', '>', $after->sort_order)->orderBy('sort_order')->value('sort_order');

        if ($next === null) {
            return $after->sort_order + 10;
        }
        if ($next - $after->sort_order >= 2) {
            return intdiv($after->sort_order + $next, 2);
        }

        // No gap left: renumber this area in steps of 10, then insert after.
        $i = 0;
        foreach (ServiceSection::where('service_id', $serviceId)->where('area', $after->area)->orderBy('sort_order')->orderBy('id')->get() as $s) {
            $s->sort_order = (++$i) * 10;
            $s->save();
            if ($s->id === $after->id) {
                $i++;
            }
        }

        return $after->fresh()->sort_order + 10;
    }

    /**
     * Section content is Blade, so it runs as code. Reject PHP that isn't already
     * in the section, dangerous function calls, and Blade that doesn't compile.
     */
    protected function guardBlade($content, $previous = '')
    {
        $errors = [];

        if (preg_match('/<\?(php|=)?/i', $content)) {
            $errors[] = 'Raw PHP tags (<?php) are not allowed.';
        }

        preg_match_all('/@php\b.*?@endphp/s', $content, $new);
        preg_match_all('/@php\b.*?@endphp/s', (string) $previous, $old);
        $allowed = array_merge($old[0], ['@php $s = $key; @endphp']);
        foreach ($new[0] as $block) {
            if (!in_array(trim(preg_replace('/\s+/', ' ', $block)), array_map(function ($b) {
                return trim(preg_replace('/\s+/', ' ', $b));
            }, $allowed), true)) {
                $errors[] = 'New @php blocks are not allowed: ' . mb_substr($block, 0, 80);
            }
        }

        $danger = 'exec|shell_exec|system|passthru|proc_open|popen|eval|assert|create_function|file_put_contents|file_get_contents|fopen|unlink|rmdir|include|require|include_once|require_once|call_user_func|call_user_func_array|base64_decode|env|config|app|resolve|DB|Artisan|Storage|File|Schema|Http|Mail|Auth|Hash|Crypt|Session|Cache|request|session|ini_set|putenv|header|exit|die';
        if (preg_match_all('/(\{\{|\{!!|@\w+\s*\()(.{0,300}?)(\}\}|!!\}|\))/s', $content, $exprs)) {
            foreach ($exprs[2] as $expr) {
                if (preg_match('/(?<![\w$>])(' . $danger . ')\s*(\(|::)/i', $expr, $hit)) {
                    $errors[] = "Calling '{$hit[1]}' inside Blade is not allowed.";
                }
                if (strpos($expr, '`') !== false) {
                    $errors[] = 'Backticks are not allowed inside Blade expressions.';
                }
            }
        }

        if (!$errors) {
            try {
                token_get_all(Blade::compileString($content), TOKEN_PARSE);
            } catch (\Throwable $e) {
                $errors[] = 'Blade does not compile: ' . $e->getMessage() . ' (check that every @if/@foreach has its @endif/@endforeach).';
            }
        }

        if ($errors) {
            throw ValidationException::withMessages(['content' => array_values(array_unique($errors))]);
        }
    }

    protected function revision(ServiceSection $section, $action)
    {
        ServiceSectionRevision::create([
            'section_id' => $section->id,
            'service_id' => $section->service_id,
            'action' => $action,
            'snapshot' => $section->only(['area', 'section_key', 'type', 'title', 'content', 'sort_order', 'is_active']),
        ]);
    }

    protected function format(ServiceSection $s, $withContent)
    {
        $out = [
            'id' => $s->id,
            'service_id' => $s->service_id,
            'area' => $s->area,
            'section_key' => $s->section_key,
            'type' => $s->type,
            'title' => $s->title,
            'sort_order' => (int) $s->sort_order,
            'is_active' => (bool) $s->is_active,
            'content_length' => mb_strlen((string) $s->content),
            'updated_at' => optional($s->updated_at)->toIso8601String(),
        ];
        if ($withContent) {
            $out['content'] = $s->content;
        }

        return $out;
    }
}
