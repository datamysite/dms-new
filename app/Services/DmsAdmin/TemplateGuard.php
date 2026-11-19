<?php

namespace App\Services\DmsAdmin;

use Illuminate\Support\Facades\Blade;
use Symfony\Component\Process\Process;

/**
 * Decides which Blade files the API may touch and checks a new version
 * compiles before it is written.
 */
class TemplateGuard
{
    /** Normalise "web.includes.footer" or "web/includes/footer.blade.php" to a relative path. */
    public static function normalise(string $name): ?string
    {
        $name = trim(str_replace('\\', '/', $name));
        if ($name === '' || str_contains($name, "\0")) {
            return null;
        }
        if (! str_ends_with($name, '.blade.php')) {
            $name = str_replace('.', '/', $name).'.blade.php';
        }
        $name = ltrim($name, '/');
        // No traversal, no hidden files.
        foreach (explode('/', $name) as $part) {
            if ($part === '' || $part === '.' || $part === '..' || str_starts_with($part, '.')) {
                return null;
            }
        }
        return $name;
    }

    public static function allowed(string $relative): bool
    {
        if (in_array($relative, config('dms_admin.templates', []), true)) {
            return true;
        }
        foreach (config('dms_admin.template_dirs', []) as $dir) {
            $dir = trim($dir, '/').'/';
            if (str_starts_with($relative, $dir)) {
                return true;
            }
        }
        return false;
    }

    public static function absolute(string $relative): string
    {
        return resource_path('views/'.$relative);
    }

    /** Every allowed file that exists, with size and modified time. */
    public static function listAllowed(): array
    {
        $out = [];
        $seen = [];
        $add = function (string $rel) use (&$out, &$seen) {
            if (isset($seen[$rel])) {
                return;
            }
            $abs = static::absolute($rel);
            if (is_file($abs)) {
                $seen[$rel] = true;
                $out[] = [
                    'path' => $rel,
                    'view' => str_replace('/', '.', substr($rel, 0, -10)),
                    'bytes' => filesize($abs),
                    'modified' => date('c', filemtime($abs)),
                ];
            }
        };
        foreach (config('dms_admin.templates', []) as $rel) {
            $add($rel);
        }
        foreach (config('dms_admin.template_dirs', []) as $dir) {
            $base = resource_path('views/'.trim($dir, '/'));
            if (! is_dir($base)) {
                continue;
            }
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS));
            foreach ($it as $file) {
                if (str_ends_with($file->getFilename(), '.blade.php')) {
                    $add(ltrim(str_replace('\\', '/', substr($file->getPathname(), strlen(resource_path('views')))), '/'));
                }
            }
        }
        usort($out, fn ($a, $b) => strcmp($a['path'], $b['path']));
        return $out;
    }

    /**
     * Compile the Blade source and lint the resulting PHP.
     * Returns null when fine, or an error message.
     */
    public static function check(string $source): ?string
    {
        try {
            $php = Blade::compileString($source);
        } catch (\Throwable $e) {
            return 'Blade compile error: '.$e->getMessage();
        }

        $tmp = tempnam(sys_get_temp_dir(), 'dmsblade');
        file_put_contents($tmp, $php);
        try {
            $proc = new Process([PHP_BINARY, '-l', $tmp]);
            $proc->setTimeout(20);
            $proc->run();
            if (! $proc->isSuccessful()) {
                $msg = trim($proc->getOutput().' '.$proc->getErrorOutput());
                return 'PHP syntax error after Blade compile: '.str_replace($tmp, '(template)', $msg);
            }
        } catch (\Throwable $e) {
            // php -l not runnable on this host: fall back to compile-only check.
        } finally {
            @unlink($tmp);
        }

        // Directive balance (cheap sanity check for the common mistakes).
        foreach ([['@foreach', '@endforeach'], ['@if', '@endif'], ['@section', '@endsection'], ['@push', '@endpush']] as [$open, $close]) {
            $o = preg_match_all('/'.preg_quote($open, '/').'\b(?!\w)/', $source);
            $c = substr_count($source, $close);
            if ($open === '@section') {
                // one-line @section('x', 'y') has no @endsection
                $o -= preg_match_all("/@section\s*\([^)]*,[^)]*\)/", $source);
            }
            if ($o !== $c) {
                return "Unbalanced {$open} ({$o}) / {$close} ({$c}).";
            }
        }
        return null;
    }
}
