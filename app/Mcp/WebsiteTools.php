<?php

namespace App\Mcp;

/**
 * The tools Claude sees. Each tool maps to one of your /api/v1 routes.
 * To add a tool: add an entry to definitions() and a case to call().
 * Redirect tools (11 Nov 2026) call the routes in routes/api-redirects.php.
 */
class WebsiteTools
{
    protected $api;

    public function __construct(ApiBridge $api)
    {
        $this->api = $api;
    }

    public function definitions()
    {
        $str = function ($description) {
            return ['type' => 'string', 'description' => $description];
        };
        $int = function ($description) {
            return ['type' => 'integer', 'description' => $description];
        };
        $fields = [
            'type' => 'object',
            'additionalProperties' => true,
            'description' => 'Blog fields exactly as the blog API expects them (call get_blog on an existing post first to see the field names and formats).',
        ];
        $metaFields = [
            'url' => $str('Page URL or path, e.g. "/seo-services" or "https://datamysite.com/seo-services".'),
            'title' => $str('Meta title (aim for 50-60 characters).'),
            'description' => $str('Meta description (aim for 140-160 characters).'),
            'keywords' => $str('Comma-separated meta keywords.'),
            'h1_content' => $str('The H1 heading for the page.'),
        ];

        return [
            // ------------------------------------------------------------ meta tags
            [
                'name' => 'list_meta_tags',
                'title' => 'List meta tags',
                'description' => 'List pages with their meta title, description, keywords and H1. Filter with search, or missing="h1"/"keywords" to find gaps.',
                'inputSchema' => $this->schema([
                    'search' => $str('Text to find in the URL, title or H1.'),
                    'missing' => ['type' => 'string', 'enum' => ['h1', 'keywords'], 'description' => 'Only rows where this field is empty.'],
                    'page' => $int('Page number (default 1).'),
                    'per_page' => $int('Rows per page, max 200 (default 50).'),
                ]),
                'annotations' => ['readOnlyHint' => true, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'get_meta_tags',
                'title' => 'Get meta tags for a URL',
                'description' => 'Get the meta title, description, keywords and H1 stored for one page URL.',
                'inputSchema' => $this->schema(['url' => $metaFields['url']], ['url']),
                'annotations' => ['readOnlyHint' => true, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'upsert_meta_tags',
                'title' => 'Create or update meta tags',
                'description' => 'Create or update the meta tags / H1 for a URL. Only the fields you send are changed. Title and description are required when the URL has no row yet. Returns the previous values and SEO length warnings.',
                'inputSchema' => $this->schema($metaFields, ['url']),
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false, 'idempotentHint' => true, 'openWorldHint' => false],
            ],
            [
                'name' => 'bulk_upsert_meta_tags',
                'title' => 'Bulk create or update meta tags',
                'description' => 'Create or update meta tags for up to 100 URLs in one call. All-or-nothing: if one item is invalid nothing is saved.',
                'inputSchema' => $this->schema([
                    'items' => [
                        'type' => 'array',
                        'minItems' => 1,
                        'maxItems' => 100,
                        'items' => ['type' => 'object', 'properties' => $metaFields, 'required' => ['url'], 'additionalProperties' => false],
                    ],
                ], ['items']),
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false, 'idempotentHint' => true, 'openWorldHint' => false],
            ],
            [
                'name' => 'delete_meta_tags',
                'title' => 'Delete meta tags',
                'description' => 'Delete the meta tags row with this id. Only use when the user explicitly asks to delete.',
                'inputSchema' => $this->schema(['id' => $int('The meta tags row id (from list_meta_tags or get_meta_tags).')], ['id']),
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => true, 'openWorldHint' => false],
            ],

            // ------------------------------------------------------- service sections
            [
                'name' => 'list_services',
                'title' => 'List service pages',
                'description' => 'List all service pages with id, name, slug, parent_id, URL path and how many sections each has.',
                'inputSchema' => $this->schema([
                    'search' => $str('Text to find in the name or URL path.'),
                    'parent_id' => $int('Only children of this service id (0 = top-level services).'),
                ]),
                'annotations' => ['readOnlyHint' => true, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'get_service_sections',
                'title' => 'Get the sections of a service page',
                'description' => 'List the sections of one service page in display order (id, area, section_key, type, title, sort_order, is_active). Set with_content=true to include the Blade content of every section (large).',
                'inputSchema' => $this->schema([
                    'service' => $str('Service id, slug (e.g. "pink-taxi") or page URL/path.'),
                    'with_content' => ['type' => 'boolean', 'description' => 'Include each section\'s Blade content.'],
                ], ['service']),
                'annotations' => ['readOnlyHint' => true, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'get_service_section',
                'title' => 'Get one section',
                'description' => 'Get one section with its full Blade content.',
                'inputSchema' => $this->schema(['id' => $int('Section id.')], ['id']),
                'annotations' => ['readOnlyHint' => true, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'update_service_section',
                'title' => 'Update a section',
                'description' => 'Change a section. Either send the full new "content", or "find" + "replace" for a small edit (the find text must occur exactly once). Can also change title, is_active (hide/show), sort_order, area, type or section_key. Content is Blade: keep {{ }} expressions, @include, @foreach/@endforeach and @csrf intact. A revision is saved first.',
                'inputSchema' => $this->schema([
                    'id' => $int('Section id.'),
                    'content' => $str('Full new Blade content of the section.'),
                    'find' => $str('Exact text to replace (must occur once in the section).'),
                    'replace' => $str('Replacement text for "find".'),
                    'title' => $str('Admin label of the section.'),
                    'is_active' => ['type' => 'boolean', 'description' => 'false hides the section on the website.'],
                    'sort_order' => $int('Position (sections are sorted ascending, normally in steps of 10).'),
                    'area' => ['type' => 'string', 'enum' => ['head', 'main', 'after_main', 'scripts']],
                    'type' => ['type' => 'string', 'enum' => ['style', 'hero', 'content', 'include', 'subservices', 'blogs', 'faq', 'script']],
                    'section_key' => $str('Unique key within the page, lowercase-with-dashes.'),
                ], ['id']),
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'create_service_section',
                'title' => 'Add a section to a service page',
                'description' => 'Add a new section. Use after_section_key to place it right after an existing section (e.g. after "what-is-hoarding-advertising"); otherwise it goes at the end of its area (before you know the order, call get_service_sections). Copy the HTML structure/classes of a neighbouring section so it matches the site design.',
                'inputSchema' => $this->schema([
                    'service' => $str('Service id, slug or page URL/path.'),
                    'section_key' => $str('Unique key within the page, lowercase-with-dashes, e.g. "pricing".'),
                    'title' => $str('Admin label, usually the section heading.'),
                    'content' => $str('Blade/HTML content of the section.'),
                    'after_section_key' => $str('Insert right after this section.'),
                    'area' => ['type' => 'string', 'enum' => ['head', 'main', 'after_main', 'scripts'], 'description' => 'Default main.'],
                    'type' => ['type' => 'string', 'enum' => ['style', 'hero', 'content', 'include', 'subservices', 'blogs', 'faq', 'script'], 'description' => 'Default content.'],
                    'is_active' => ['type' => 'boolean', 'description' => 'Default true. Use false to add it hidden.'],
                ], ['service', 'section_key', 'content']),
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'reorder_service_sections',
                'title' => 'Reorder sections',
                'description' => 'Set the order of sections on a page: pass section ids in the new order (sort_order becomes 10, 20, 30...). Usually only the "main" area sections.',
                'inputSchema' => $this->schema([
                    'service' => $str('Service id, slug or page URL/path.'),
                    'order' => ['type' => 'array', 'items' => ['type' => 'integer'], 'description' => 'Section ids in the new order.'],
                ], ['service', 'order']),
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false, 'idempotentHint' => true, 'openWorldHint' => false],
            ],
            [
                'name' => 'delete_service_section',
                'title' => 'Delete a section',
                'description' => 'Delete a section. Prefer update_service_section with is_active=false to hide it. Only delete when the user explicitly asks. It can be brought back with restore_service_section_revision.',
                'inputSchema' => $this->schema(['id' => $int('Section id.')], ['id']),
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => true, 'openWorldHint' => false],
            ],
            [
                'name' => 'list_service_section_revisions',
                'title' => 'Section history',
                'description' => 'List saved versions of a section (newest first), or with service set and no id, the deleted sections of that page.',
                'inputSchema' => $this->schema([
                    'id' => $int('Section id.'),
                    'service' => $str('Service id/slug/path: list its deleted sections instead.'),
                ]),
                'annotations' => ['readOnlyHint' => true, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'restore_service_section_revision',
                'title' => 'Restore a section version',
                'description' => 'Put a section back to a saved version (also brings back a deleted section). The current version is saved first.',
                'inputSchema' => $this->schema(['revision_id' => $int('Revision id from list_service_section_revisions.')], ['revision_id']),
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false, 'openWorldHint' => false],
            ],

            // ---------------------------------------------------------------- blogs
            [
                'name' => 'list_blogs',
                'title' => 'List blogs',
                'description' => 'List blog posts. Any filters are passed to the blog API as query parameters.',
                'inputSchema' => $this->schema([
                    'query' => ['type' => 'object', 'additionalProperties' => true, 'description' => 'Optional query parameters, e.g. {"search":"billboard","page":2}.'],
                ]),
                'annotations' => ['readOnlyHint' => true, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'get_blog',
                'title' => 'Get a blog post',
                'description' => 'Get one blog post with all its fields.',
                'inputSchema' => $this->schema(['blog' => $str('Blog id (or slug, if your API accepts slugs).')], ['blog']),
                'annotations' => ['readOnlyHint' => true, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'create_blog',
                'title' => 'Create a blog post',
                'description' => 'Create a new blog post (as a draft unless the fields say otherwise). Use publish_blog separately, only when the user asks to publish.',
                'inputSchema' => $this->schema(['fields' => $fields], ['fields']),
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'update_blog',
                'title' => 'Update a blog post',
                'description' => 'Update an existing blog post. Send only the fields to change.',
                'inputSchema' => $this->schema(['blog' => $str('Blog id.'), 'fields' => $fields], ['blog', 'fields']),
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false, 'idempotentHint' => true, 'openWorldHint' => false],
            ],
            [
                'name' => 'publish_blog',
                'title' => 'Publish a blog post',
                'description' => 'Publish a blog post so it is live on the website. Only call this when the user has asked to publish.',
                'inputSchema' => $this->schema(['blog' => $str('Blog id.')], ['blog']),
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false, 'idempotentHint' => true, 'openWorldHint' => true],
            ],

            // ------------------------------------------------------------ redirects
            [
                'name' => 'list_redirects',
                'title' => 'List redirects',
                'description' => 'List the 301 redirects stored in the database. Filter with search (old or new URL), active, or target (redirects that point at this path).',
                'inputSchema' => $this->schema([
                    'search' => $str('Text to find in the old URL or the target.'),
                    'active' => ['type' => 'boolean', 'description' => 'true = only active, false = only inactive.'],
                    'target' => $str('Only redirects that point at this path, e.g. "/lead-generation".'),
                    'page' => $int('Page number (default 1).'),
                    'per_page' => $int('Rows per page, max 100 (default 50).'),
                ]),
                'annotations' => ['readOnlyHint' => true, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'get_redirect',
                'title' => 'Get a redirect',
                'description' => 'Get one redirect by id, with its hit count.',
                'inputSchema' => $this->schema(['id' => $int('Redirect id (from list_redirects).')], ['id']),
                'annotations' => ['readOnlyHint' => true, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'upsert_redirect',
                'title' => 'Create or update a redirect',
                'description' => 'Create a 301 redirect from an old URL to a live page, or change the one that already exists for that old URL. Refused (nothing saved) for protected Lock-List URLs, chains, loops, targets of / or /blogs, a published blog at the old URL, or a target that does not answer 200. Run with dry_run=true first to see the checks. A revision is saved.',
                'inputSchema' => $this->schema([
                    'from_path' => $str('Old URL path, e.g. "/old-slug" (a full datamysite.com URL also works).'),
                    'to_url' => $str('Target path, e.g. "/new-page".'),
                    'status_code' => ['type' => 'integer', 'enum' => [301, 302, 307, 308], 'description' => 'Default 301.'],
                    'is_active' => ['type' => 'boolean', 'description' => 'Default true. false keeps the row but stops redirecting.'],
                    'note' => $str('Why, e.g. "C-16 tab 7".'),
                    'retarget_incoming' => ['type' => 'boolean', 'description' => 'Move redirects that point at from_path to the new target in the same save.'],
                    'allow_soft404' => ['type' => 'boolean', 'description' => 'Allow a target of / or /blogs (only when the user asked).'],
                    'allow_live_source' => ['type' => 'boolean', 'description' => 'Allow redirecting the URL of a published blog post (only when the user asked).'],
                    'skip_target_check' => ['type' => 'boolean', 'description' => 'Do not request the target before saving.'],
                    'dry_run' => ['type' => 'boolean', 'description' => 'Run every check and save nothing.'],
                ], ['from_path', 'to_url']),
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false, 'idempotentHint' => true, 'openWorldHint' => false],
            ],
            [
                'name' => 'update_redirect',
                'title' => 'Update a redirect',
                'description' => 'Change the target, status code, active flag or note of a redirect by id. Same checks and flags as upsert_redirect. The old URL itself cannot change: delete and create instead.',
                'inputSchema' => $this->schema([
                    'id' => $int('Redirect id.'),
                    'to_url' => $str('New target path.'),
                    'status_code' => ['type' => 'integer', 'enum' => [301, 302, 307, 308]],
                    'is_active' => ['type' => 'boolean'],
                    'note' => $str('Why.'),
                    'retarget_incoming' => ['type' => 'boolean'],
                    'allow_soft404' => ['type' => 'boolean'],
                    'allow_live_source' => ['type' => 'boolean'],
                    'skip_target_check' => ['type' => 'boolean'],
                    'dry_run' => ['type' => 'boolean'],
                ], ['id']),
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false, 'idempotentHint' => true, 'openWorldHint' => false],
            ],
            [
                'name' => 'delete_redirect',
                'title' => 'Delete a redirect',
                'description' => 'Delete a redirect: the old URL stops redirecting (it usually becomes a 404). Prefer update_redirect with is_active=false. Only delete when the user explicitly asks. It can be brought back with undo_redirect_revision.',
                'inputSchema' => $this->schema(['id' => $int('Redirect id.')], ['id']),
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => true, 'openWorldHint' => false],
            ],
            [
                'name' => 'list_redirect_revisions',
                'title' => 'Redirect history',
                'description' => 'List saved changes to redirects (newest first), for one old URL or one redirect id.',
                'inputSchema' => $this->schema([
                    'from_path' => $str('Old URL path.'),
                    'redirect_id' => $int('Redirect id.'),
                    'page' => $int('Page number (default 1).'),
                    'per_page' => $int('Rows per page, max 100 (default 50).'),
                ]),
                'annotations' => ['readOnlyHint' => true, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'undo_redirect_revision',
                'title' => 'Undo a redirect change',
                'description' => 'Put a redirect back to how it was before one saved change (undoing a create removes it, undoing a delete brings it back). The undo is saved as a revision too.',
                'inputSchema' => $this->schema(['revision_id' => $int('Revision id from list_redirect_revisions.')], ['revision_id']),
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'check_urls',
                'title' => 'Check URLs and redirects',
                'description' => 'The server requests up to 20 paths on datamysite.com and reports every redirect hop and the final status code. Use it to verify a redirect (one 301 hop, then 200) or that a page loads.',
                'inputSchema' => $this->schema([
                    'paths' => $str('Comma-separated paths, e.g. "/old-one,/old-two".'),
                ], ['paths']),
                'annotations' => ['readOnlyHint' => true, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'audit_redirects',
                'title' => 'Audit redirects',
                'description' => 'Report problems across all redirects: chains, loops, redirects to / or /blogs (soft 404), old URLs that differ only by case, and published blog posts hidden behind a redirect.',
                'inputSchema' => $this->schema([]),
                'annotations' => ['readOnlyHint' => true, 'destructiveHint' => false, 'openWorldHint' => false],
            ],

            // ------------------------------------------- admin API (12 Nov 2026)
            // These call /api/v1/admin/* with DMS_ADMIN_API_TOKEN (see adminCall()).

            // templates
            [
                'name' => 'list_templates',
                'title' => 'List editable templates',
                'description' => 'List the Blade template files the admin API may read and edit (footer, nav, blog detail, category, schema include...), with size and last change.',
                'inputSchema' => $this->schema([]),
                'annotations' => ['readOnlyHint' => true, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'get_template',
                'title' => 'Read a template',
                'description' => 'Read one whitelisted Blade template. Pass search to get only the matching lines with 3 lines of context each (much smaller than the whole file).',
                'inputSchema' => $this->schema([
                    'path' => $str('Template path relative to resources/views, e.g. "web/includes/footer.blade.php" or "web.includes.footer".'),
                    'search' => $str('Optional text to find; returns only matching lines with context.'),
                ], ['path']),
                'annotations' => ['readOnlyHint' => true, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'update_template',
                'title' => 'Edit a template',
                'description' => 'Edit a whitelisted Blade template that renders on every page using it. Send either the full "content" or "find" + "replace" (find must occur once unless replace_all=true). Always call with dry_run=true first. The edit is refused if Blade does not compile, PHP does not lint, or @foreach/@if/@section/@push are unbalanced. A revision is saved, compiled views are cleared and LiteSpeed is purged. Keep {{ }}, @include, @foreach and @csrf intact.',
                'inputSchema' => $this->schema([
                    'path' => $str('Template path relative to resources/views.'),
                    'content' => $str('Full new content of the file.'),
                    'find' => $str('Exact text to replace.'),
                    'replace' => $str('Replacement text for "find".'),
                    'replace_all' => ['type' => 'boolean', 'description' => 'Replace every occurrence of find.'],
                    'dry_run' => ['type' => 'boolean', 'description' => 'Run every check and save nothing.'],
                    'note' => $str('Why, e.g. "TT-18 remove symbol from H1".'),
                ], ['path']),
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'list_template_revisions',
                'title' => 'Template history',
                'description' => 'List saved "before" copies of a template (newest first).',
                'inputSchema' => $this->schema(['path' => $str('Template path relative to resources/views.')], ['path']),
                'annotations' => ['readOnlyHint' => true, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'restore_template_revision',
                'title' => 'Restore a template version',
                'description' => 'Put a template back to a saved revision. The current version is saved as a new revision first.',
                'inputSchema' => $this->schema(['revision_id' => $int('Revision id from list_template_revisions.')], ['revision_id']),
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false, 'openWorldHint' => false],
            ],

            // routes/redirects.php
            [
                'name' => 'list_file_redirects',
                'title' => 'List routes-file redirects',
                'description' => 'List the Route::redirect lines in routes/redirects.php (line, from, to, status, active). These are separate from the redirects table and win over a disabled table row.',
                'inputSchema' => $this->schema(['search' => $str('Text to find in the old or new URL.')]),
                'annotations' => ['readOnlyHint' => true, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'update_file_redirect',
                'title' => 'Change a routes-file redirect',
                'description' => 'Disable (comment out), re-enable or retarget a Route::redirect line in routes/redirects.php. The file is linted before saving, a revision is kept and the route cache is cleared.',
                'inputSchema' => $this->schema([
                    'from' => $str('Old URL path of the line, e.g. "/top-outdoor-advertising-spots-sharjah".'),
                    'action' => ['type' => 'string', 'enum' => ['disable', 'enable', 'retarget']],
                    'to' => $str('New target path (only for retarget).'),
                    'note' => $str('Why, e.g. "TT-02 C-16".'),
                ], ['from', 'action']),
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'restore_file_redirects',
                'title' => 'Restore routes/redirects.php',
                'description' => 'Put routes/redirects.php back to a saved revision (revision id from an update_file_redirect result).',
                'inputSchema' => $this->schema(['revision_id' => $int('Revision id.')], ['revision_id']),
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false, 'openWorldHint' => false],
            ],

            // cache, IndexNow, sitemap
            [
                'name' => 'purge_cache',
                'title' => 'Purge cache',
                'description' => 'Send a LiteSpeed purge for the given paths (all pages when paths is empty) and clear Laravel view/app cache. Optional routes=true also clears the route cache.',
                'inputSchema' => $this->schema([
                    'paths' => ['type' => 'array', 'items' => ['type' => 'string'], 'description' => 'Paths to purge, e.g. ["/website-development"]. Empty = everything.'],
                    'views' => ['type' => 'boolean', 'description' => 'Clear compiled views (default true).'],
                    'routes' => ['type' => 'boolean', 'description' => 'Also clear the route cache.'],
                ]),
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false, 'idempotentHint' => true, 'openWorldHint' => true],
            ],
            [
                'name' => 'submit_indexnow',
                'title' => 'Submit URLs to IndexNow',
                'description' => 'Submit specific URLs (paths or full URLs) to IndexNow. Every call is logged (see get_indexnow_log).',
                'inputSchema' => $this->schema([
                    'urls' => ['type' => 'array', 'items' => ['type' => 'string'], 'minItems' => 1, 'description' => 'Paths or full URLs.'],
                ], ['urls']),
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false, 'idempotentHint' => true, 'openWorldHint' => true],
            ],
            [
                'name' => 'get_indexnow_log',
                'title' => 'IndexNow log',
                'description' => 'The last 50 IndexNow submissions with URL count, HTTP status and any error.',
                'inputSchema' => $this->schema([]),
                'annotations' => ['readOnlyHint' => true, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'audit_sitemap',
                'title' => 'Audit sitemap',
                'description' => 'Compare the live sitemap.xml with the database: URLs in it that redirect or are not expected, and published URLs missing from it.',
                'inputSchema' => $this->schema([]),
                'annotations' => ['readOnlyHint' => true, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'rebuild_sitemap',
                'title' => 'Rebuild sitemap from the database',
                'description' => 'Rewrite sitemap.xml from the database (published blogs + service pages, minus redirects, excluded paths and drafts). Only use when the user asks or when /update-sitemap keeps including redirecting URLs.',
                'inputSchema' => $this->schema([]),
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false, 'idempotentHint' => true, 'openWorldHint' => false],
            ],

            // live-page inspection
            [
                'name' => 'inspect_page',
                'title' => 'Inspect a live page',
                'description' => 'Fetch a live page as a visitor gets it and report title, meta, canonical, robots, H1s, H2/H3s, schema types, word count, "[" placeholders, images without alt, links that hit a redirect and whether an author box is present. text=true adds the page text; find="..." returns only snippets around that text.',
                'inputSchema' => $this->schema([
                    'path' => $str('Page path, e.g. "/website-development".'),
                    'text' => ['type' => 'boolean', 'description' => 'Include the visible page text.'],
                    'nocache' => ['type' => 'boolean', 'description' => 'Add a cache-busting query string.'],
                    'find' => $str('Return only snippets around this text, e.g. "AED 1,500".'),
                ], ['path']),
                'annotations' => ['readOnlyHint' => true, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'inspect_pages',
                'title' => 'Inspect up to 20 live pages',
                'description' => 'Summary inspection of up to 20 live pages in one call (status, title, meta length, H1, schema types, placeholders, images without alt, links to redirects, author box).',
                'inputSchema' => $this->schema([
                    'paths' => ['type' => 'array', 'items' => ['type' => 'string'], 'minItems' => 1, 'maxItems' => 20],
                ], ['paths']),
                'annotations' => ['readOnlyHint' => true, 'destructiveHint' => false, 'openWorldHint' => false],
            ],

            // data clean-up
            [
                'name' => 'list_duplicate_meta',
                'title' => 'List duplicate meta rows',
                'description' => 'URLs that have more than one meta tags row, with every row (id, title, description).',
                'inputSchema' => $this->schema([]),
                'annotations' => ['readOnlyHint' => true, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'delete_meta_row',
                'title' => 'Delete a duplicate meta row',
                'description' => 'Delete one duplicate meta tags row by id. Refused if it is the only row for its URL. A copy is saved as a revision.',
                'inputSchema' => $this->schema(['id' => $int('Meta row id from list_duplicate_meta.')], ['id']),
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => true, 'openWorldHint' => false],
            ],
            [
                'name' => 'list_blogs_hidden_by_redirect',
                'title' => 'Blogs hidden behind a redirect',
                'description' => 'Published blogs whose slug also has an active redirect (hidden behind the 301 but still in listings and related posts).',
                'inputSchema' => $this->schema([]),
                'annotations' => ['readOnlyHint' => true, 'destructiveHint' => false, 'openWorldHint' => false],
            ],
            [
                'name' => 'unpublish_blogs_hidden_by_redirect',
                'title' => 'Set redirected blogs to draft',
                'description' => 'Set every published blog that already redirects to draft (status 0), so listings stop showing it. The redirect keeps working.',
                'inputSchema' => $this->schema([]),
                'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false, 'idempotentHint' => true, 'openWorldHint' => false],
            ],
        ];
    }

    /**
     * @return array{status:int, body:mixed}
     */
    public function call($name, array $args)
    {
        $id = function ($key) use ($args) {
            return rawurlencode((string) (isset($args[$key]) ? $args[$key] : ''));
        };

        switch ($name) {
            case 'list_meta_tags':
                return $this->api->call('GET', 'meta-tags', array_filter($args, function ($v) {
                    return $v !== null && $v !== '';
                }));
            case 'get_meta_tags':
                return $this->api->call('GET', 'meta-tags/lookup', ['url' => (string) $args['url']]);
            case 'upsert_meta_tags':
                return $this->api->call('POST', 'meta-tags/upsert', $args);
            case 'bulk_upsert_meta_tags':
                return $this->api->call('POST', 'meta-tags/bulk-upsert', ['items' => $args['items']]);
            case 'delete_meta_tags':
                return $this->api->call('DELETE', 'meta-tags/' . (int) $args['id']);

            case 'list_services':
                return $this->api->call('GET', 'services', array_filter($args, function ($v) {
                    return $v !== null && $v !== '';
                }));
            case 'get_service_sections':
                return $this->api->call('GET', 'services/' . $this->service($args) . '/sections', ['with_content' => !empty($args['with_content']) ? 1 : 0]);
            case 'get_service_section':
                return $this->api->call('GET', 'service-sections/' . (int) $args['id']);
            case 'update_service_section':
                $fields = $args;
                unset($fields['id']);
                return $this->api->call('PATCH', 'service-sections/' . (int) $args['id'], $fields);
            case 'create_service_section':
                $fields = $args;
                unset($fields['service']);
                return $this->api->call('POST', 'services/' . $this->service($args) . '/sections', $fields);
            case 'reorder_service_sections':
                return $this->api->call('POST', 'services/' . $this->service($args) . '/sections/reorder', ['order' => $args['order']]);
            case 'delete_service_section':
                return $this->api->call('DELETE', 'service-sections/' . (int) $args['id']);
            case 'list_service_section_revisions':
                if (!empty($args['id'])) {
                    return $this->api->call('GET', 'service-sections/' . (int) $args['id'] . '/revisions');
                }
                if (!empty($args['service'])) {
                    return $this->api->call('GET', 'services/' . $this->service($args) . '/deleted-sections');
                }
                return ['status' => 422, 'body' => ['message' => 'Pass a section id, or a service to list its deleted sections.']];
            case 'restore_service_section_revision':
                return $this->api->call('POST', 'service-section-revisions/' . (int) $args['revision_id'] . '/restore');

            case 'list_blogs':
                return $this->api->call('GET', 'blogs', isset($args['query']) && is_array($args['query']) ? $args['query'] : []);
            case 'get_blog':
                return $this->api->call('GET', 'blogs/' . $id('blog'));
            case 'create_blog':
                return $this->api->call('POST', 'blogs', (array) $args['fields']);
            case 'update_blog':
                return $this->api->call('PATCH', 'blogs/' . $id('blog'), (array) $args['fields']);
            case 'publish_blog':
                return $this->api->call('POST', 'blogs/' . $id('blog') . '/publish');

            case 'list_redirects':
                $query = array_filter($args, function ($v) {
                    return $v !== null && $v !== '';
                });
                if (array_key_exists('active', $query)) {
                    $query['active'] = $query['active'] ? 1 : 0;
                }
                return $this->api->call('GET', 'redirects', $query);
            case 'get_redirect':
                return $this->api->call('GET', 'redirects/' . (int) $args['id']);
            case 'upsert_redirect':
                return $this->api->call('POST', 'redirects', $args);
            case 'update_redirect':
                $fields = $args;
                unset($fields['id']);
                return $this->api->call('PATCH', 'redirects/' . (int) $args['id'], $fields);
            case 'delete_redirect':
                return $this->api->call('DELETE', 'redirects/' . (int) $args['id']);
            case 'list_redirect_revisions':
                return $this->api->call('GET', 'redirect-revisions', array_filter($args, function ($v) {
                    return $v !== null && $v !== '';
                }));
            case 'undo_redirect_revision':
                return $this->api->call('POST', 'redirect-revisions/' . (int) $args['revision_id'] . '/undo');
            case 'check_urls':
                return $this->api->call('GET', 'redirects-check', ['paths' => (string) $args['paths']]);
            case 'audit_redirects':
                return $this->api->call('GET', 'redirects-audit');

            // ------------------------------------------- admin API (12 Nov 2026)
            case 'list_templates':
                return $this->adminCall('GET', 'templates');
            case 'get_template':
                return $this->adminCall('GET', 'templates/file', $this->only($args, ['path', 'search']));
            case 'update_template':
                return $this->adminCall('POST', 'templates/file', $this->only($args, ['path', 'content', 'find', 'replace', 'replace_all', 'dry_run', 'note']));
            case 'list_template_revisions':
                return $this->adminCall('GET', 'templates/revisions', ['path' => (string) $args['path']]);
            case 'restore_template_revision':
                return $this->adminCall('POST', 'templates/revisions/' . (int) $args['revision_id'] . '/restore');

            case 'list_file_redirects':
                return $this->adminCall('GET', 'file-redirects', $this->only($args, ['search']));
            case 'update_file_redirect':
                return $this->adminCall('POST', 'file-redirects', $this->only($args, ['from', 'action', 'to', 'note']));
            case 'restore_file_redirects':
                return $this->adminCall('POST', 'file-redirects/revisions/' . (int) $args['revision_id'] . '/restore');

            case 'purge_cache':
                return $this->adminCall('POST', 'cache/purge', $this->only($args, ['paths', 'views', 'routes']));
            case 'submit_indexnow':
                return $this->adminCall('POST', 'indexnow', ['urls' => array_values((array) $args['urls'])]);
            case 'get_indexnow_log':
                return $this->adminCall('GET', 'indexnow/log');
            case 'audit_sitemap':
                return $this->adminCall('GET', 'sitemap/audit');
            case 'rebuild_sitemap':
                return $this->adminCall('POST', 'sitemap/rebuild', ['confirm' => 1]);

            case 'inspect_page':
                $q = ['path' => (string) $args['path']];
                if (!empty($args['text'])) {
                    $q['text'] = 1;
                }
                if (!empty($args['nocache'])) {
                    $q['nocache'] = 1;
                }
                if (isset($args['find']) && $args['find'] !== '') {
                    $q['find'] = (string) $args['find'];
                }
                return $this->adminCall('GET', 'inspect', $q);
            case 'inspect_pages':
                return $this->adminCall('POST', 'inspect', ['paths' => array_values((array) $args['paths'])]);

            case 'list_duplicate_meta':
                return $this->adminCall('GET', 'meta/duplicates');
            case 'delete_meta_row':
                return $this->adminCall('DELETE', 'meta/rows/' . (int) $args['id']);
            case 'list_blogs_hidden_by_redirect':
                return $this->adminCall('GET', 'blogs/hidden-by-redirect');
            case 'unpublish_blogs_hidden_by_redirect':
                return $this->adminCall('POST', 'blogs/hidden-by-redirect', ['unpublish' => 1]);
        }

        throw new \InvalidArgumentException("Unknown tool: $name");
    }

    /** Service id or slug for the URL; a full URL/path is reduced to its last segment (the slug). */
    protected function service(array $args)
    {
        $value = trim((string) (isset($args['service']) ? $args['service'] : ''));
        $path = trim((string) (parse_url($value, PHP_URL_PATH) ?: $value), '/');
        $segments = array_values(array_filter(explode('/', $path)));

        return rawurlencode($segments ? end($segments) : $value);
    }

    /**
     * Call the admin API (/api/v1/admin/...) with DMS_ADMIN_API_TOKEN.
     * Kept separate from ApiBridge because the admin routes use their own token.
     *
     * @return array{status:int, body:mixed}
     */
    protected function adminCall($method, $path, array $data = [])
    {
        $token = (string) config('dms_admin.token');
        if ($token === '') {
            return ['status' => 500, 'body' => ['message' => 'DMS_ADMIN_API_TOKEN is not set in .env on the MCP server.']];
        }

        $base = rtrim((string) config('dms_admin.api_base', config('dms_admin.site_url') . '/api/v1/admin'), '/');
        $url = $base . '/' . ltrim($path, '/');

        try {
            $request = \Illuminate\Support\Facades\Http::withToken($token)
                ->acceptJson()
                ->timeout(60);

            $method = strtoupper($method);
            if ($method === 'GET') {
                $response = $request->get($url, $data);
            } elseif ($method === 'DELETE') {
                $response = $request->delete($url, $data);
            } else {
                $response = $request->send($method, $url, ['json' => $data]);
            }

            $body = $response->json();

            return ['status' => $response->status(), 'body' => $body !== null ? $body : $response->body()];
        } catch (\Throwable $e) {
            return ['status' => 502, 'body' => ['message' => 'Admin API request failed: ' . $e->getMessage(), 'url' => $url]];
        }
    }

    /** Keep only the given keys, dropping null/empty-string values. */
    protected function only(array $args, array $keys)
    {
        $out = [];
        foreach ($keys as $k) {
            if (array_key_exists($k, $args) && $args[$k] !== null && $args[$k] !== '') {
                $out[$k] = $args[$k];
            }
        }

        return $out;
    }

    protected function schema(array $properties, array $required = [])
    {
        $schema = ['type' => 'object', 'properties' => (object) $properties];
        if ($required) {
            $schema['required'] = $required;
        }

        return $schema;
    }
}