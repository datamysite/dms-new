<?php

namespace App\Observers;

use App\Services\DmsAdmin\IndexNow;
use App\Services\DmsAdmin\LiteSpeedPurge;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * TT-17: when a blog is published or a published blog changes,
 * purge its URL (+ /blogs) and ping IndexNow. Runs after the response
 * is sent so saving in the admin is not slowed down.
 *
 * TT-09: when a blog goes live, goes back to draft, or its slug changes,
 * regenerate sitemap.xml with the site's own /update-sitemap route
 * (the same generator that builds the current 1,002-URL file).
 */
class DmsAdminPublishObserver
{
    public function saved($model): void
    {
        $status = (int) ($model->status ?? 0);
        $wasPublished = (int) ($model->getOriginal('status') ?? 0) === 1;
        if ($status !== 1 && ! $wasPublished) {
            return; // draft edits: nothing public changed
        }
        if (empty($model->slug)) {
            return;
        }

        $paths = ['/'.ltrim($model->slug, '/'), '/blogs'];

        $statusChanged = $model->wasChanged('status') || $model->wasRecentlyCreated;
        $slugChanged = $model->wasChanged('slug');
        if (config('dms_admin.sitemap.auto_on_publish') && ($statusChanged || $slugChanged)) {
            $url = rtrim(config('dms_admin.site_url'), '/').config('dms_admin.sitemap.update_route', '/update-sitemap');
            dispatch(function () use ($url) {
                try {
                    Http::timeout(60)->withoutRedirecting()->get($url);
                } catch (\Throwable $e) {
                    Log::warning('Sitemap auto-rebuild failed: '.$e->getMessage());
                }
            })->afterResponse();
        }

        if (config('dms_admin.litespeed.auto_on_publish')) {
            LiteSpeedPurge::queue($paths);
            // The header is attached by the AttachLiteSpeedPurge middleware on the admin response.
        }

        if (config('dms_admin.indexnow.auto_on_publish') && $status === 1) {
            dispatch(function () use ($paths) {
                try {
                    IndexNow::submit($paths);
                } catch (\Throwable $e) {
                    Log::warning('IndexNow auto-submit failed: '.$e->getMessage());
                }
            })->afterResponse();
        }
    }
}