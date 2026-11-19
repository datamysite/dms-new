<?php
// File: routes/api-redirects.php
// Paste these lines INSIDE the same authenticated group in routes/api.php that already holds the
// blog API routes (the group used by GET /api/v1/blogs), so the same token protects them.
// Do NOT expose them without that authentication.

use App\Http\Controllers\Api\RedirectApiController;
use Illuminate\Support\Facades\Route;

Route::get('redirects',            [RedirectApiController::class, 'index']);
Route::post('redirects',           [RedirectApiController::class, 'store']);
Route::get('redirects/{redirect}',    [RedirectApiController::class, 'show'])->whereNumber('redirect');
Route::patch('redirects/{redirect}',  [RedirectApiController::class, 'update'])->whereNumber('redirect');
Route::delete('redirects/{redirect}', [RedirectApiController::class, 'destroy'])->whereNumber('redirect');

Route::get('redirect-revisions',                   [RedirectApiController::class, 'revisions']);
Route::post('redirect-revisions/{revision}/undo',  [RedirectApiController::class, 'undo'])->whereNumber('revision');

Route::get('redirects-check', [RedirectApiController::class, 'check']);
Route::get('redirects-audit', [RedirectApiController::class, 'audit']);
