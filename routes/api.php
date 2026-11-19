<?php

use App\Http\Controllers\Api\BlogApiController;
use App\Http\Controllers\Api\MetaTagApiController;
use App\Http\Controllers\Api\ServiceSectionApiController;
use App\Http\Controllers\Api\BlogLookupController;
use App\Http\Controllers\Api\RedirectApiController;
use App\Http\Controllers\Api\DmsAdmin\FileRedirectController;
use App\Http\Controllers\Api\DmsAdmin\OpsController;
use App\Http\Controllers\Api\DmsAdmin\TemplateController;
use App\Http\Controllers\Mcp\McpServerController;
use App\Http\Middleware\VerifyMcpAccess;
use Illuminate\Support\Facades\Route;

Route::middleware('verify.api.token')->prefix('v1')->group(function () {
    // Blogs
    Route::get('blogs', [BlogApiController::class, 'index']);
    Route::get('blogs/{blog}', [BlogApiController::class, 'show']);
    Route::post('blogs', [BlogApiController::class, 'store']);
    Route::match(['put', 'patch'], 'blogs/{blog}', [BlogApiController::class, 'update']);
    Route::post('blogs/{blog}/publish', [BlogApiController::class, 'publish']);
    Route::get('blogs/lookup', BlogLookupController::class)->name('api.blogs.lookup');

    // Meta tags + H1 (static paths first so they aren't caught by {metaTag})
    Route::get('meta-tags', [MetaTagApiController::class, 'index']);
    Route::get('meta-tags/lookup', [MetaTagApiController::class, 'lookup']);
    Route::post('meta-tags/upsert', [MetaTagApiController::class, 'upsert']);
    Route::post('meta-tags/bulk-upsert', [MetaTagApiController::class, 'bulkUpsert']);
    Route::post('meta-tags', [MetaTagApiController::class, 'store']);
    Route::get('meta-tags/{metaTag}', [MetaTagApiController::class, 'show'])->whereNumber('metaTag');
    Route::match(['put', 'patch'], 'meta-tags/{metaTag}', [MetaTagApiController::class, 'update'])->whereNumber('metaTag');
    Route::delete('meta-tags/{metaTag}', [MetaTagApiController::class, 'destroy'])->whereNumber('metaTag');

    // Service pages: sections (Blade content per section, table service_sections)
    Route::get('services', [ServiceSectionApiController::class, 'services']);
    Route::get('services/{service}/sections', [ServiceSectionApiController::class, 'index']);
    Route::post('services/{service}/sections', [ServiceSectionApiController::class, 'store']);
    Route::post('services/{service}/sections/reorder', [ServiceSectionApiController::class, 'reorder']);
    Route::get('services/{service}/deleted-sections', [ServiceSectionApiController::class, 'deleted']);
    Route::get('service-sections/{id}', [ServiceSectionApiController::class, 'show'])->whereNumber('id');
    Route::match(['put', 'patch'], 'service-sections/{id}', [ServiceSectionApiController::class, 'update'])->whereNumber('id');
    Route::delete('service-sections/{id}', [ServiceSectionApiController::class, 'destroy'])->whereNumber('id');
    Route::get('service-sections/{id}/revisions', [ServiceSectionApiController::class, 'revisions'])->whereNumber('id');
    Route::post('service-section-revisions/{revision}/restore', [ServiceSectionApiController::class, 'restore'])->whereNumber('revision');



    // ← paste the 9 redirect lines HERE, before the closing });
    Route::get('redirects',            [RedirectApiController::class, 'index']);
    Route::post('redirects',           [RedirectApiController::class, 'store']);
    Route::get('redirects/{redirect}',    [RedirectApiController::class, 'show'])->whereNumber('redirect');
    Route::patch('redirects/{redirect}',  [RedirectApiController::class, 'update'])->whereNumber('redirect');
    Route::delete('redirects/{redirect}', [RedirectApiController::class, 'destroy'])->whereNumber('redirect');
    Route::get('redirect-revisions',                  [RedirectApiController::class, 'revisions']);
    Route::post('redirect-revisions/{revision}/undo', [RedirectApiController::class, 'undo'])->whereNumber('revision');
    Route::get('redirects-check', [RedirectApiController::class, 'check']);
    Route::get('redirects-audit', [RedirectApiController::class, 'audit']);

});

// Claude connector (MCP). URL: https://yoursite.com/api/mcp  or  https://yoursite.com/api/mcp/<access key>
Route::match(['get', 'post', 'delete'], 'mcp/{key?}', [\App\Http\Controllers\Mcp\McpServerController::class, 'handle'])
    ->middleware(\App\Http\Middleware\VerifyMcpAccess::class)
    ->where('key', '[A-Za-z0-9]{32,128}');
