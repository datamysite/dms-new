<?php

use App\Http\Controllers\Api\DmsAdmin\FileRedirectController;
use App\Http\Controllers\Api\DmsAdmin\OpsController;
use App\Http\Controllers\Api\DmsAdmin\TemplateController;
use Illuminate\Support\Facades\Route;

/*
| Loaded by App\Providers\DmsAdminServiceProvider under
| prefix api/v1/admin, middleware: api + DmsAdminToken (Bearer token).
*/

// Templates (Blade) - TT-12, TT-13, TT-14, TT-16, TT-18, TT-20, TT-21, TT-22, TT-23, TT-24, T-217, T-218, T-232
Route::get('templates', [TemplateController::class, 'index']);
Route::get('templates/file', [TemplateController::class, 'show']);            // ?path=web/includes/footer.blade.php[&search=...]
Route::post('templates/file', [TemplateController::class, 'update']);         // path + content | find/replace [+ dry_run]
Route::get('templates/revisions', [TemplateController::class, 'revisions']);  // ?path=...
Route::post('templates/revisions/{id}/restore', [TemplateController::class, 'restore'])->whereNumber('id');

// routes/redirects.php - TT-02, T-228-type leftovers
Route::get('file-redirects', [FileRedirectController::class, 'index']);       // ?search=
Route::post('file-redirects', [FileRedirectController::class, 'update']);     // from + action disable|enable|retarget (+to)
Route::post('file-redirects/revisions/{id}/restore', [FileRedirectController::class, 'restore'])->whereNumber('id');

// Cache, IndexNow, sitemap - T-237, TT-17, TT-09
Route::post('cache/purge', [OpsController::class, 'purge']);                 // paths[] or none = all
Route::post('indexnow', [OpsController::class, 'indexnow']);                  // urls[]
Route::get('indexnow/log', [OpsController::class, 'indexnowLog']);
Route::get('sitemap/audit', [OpsController::class, 'sitemapAudit']);
Route::post('sitemap/rebuild', [OpsController::class, 'sitemapRebuild']);     // confirm=1

// Live-page checks - TT-23, T-229, T-230, TT-10, TT-15 verification
Route::get('inspect', [OpsController::class, 'inspect']);                     // ?path=&text=&nocache=&find=
Route::post('inspect', [OpsController::class, 'inspectMany']);                // paths[] (max 20)

// Data clean-up - TT-15 duplicate meta rows, blogs hidden behind redirects
Route::get('meta/duplicates', [OpsController::class, 'metaDuplicates']);
Route::delete('meta/rows/{id}', [OpsController::class, 'metaDeleteRow'])->whereNumber('id');
Route::get('blogs/hidden-by-redirect', [OpsController::class, 'hiddenBlogs']);
Route::post('blogs/hidden-by-redirect', [OpsController::class, 'hiddenBlogs']); // unpublish=1
