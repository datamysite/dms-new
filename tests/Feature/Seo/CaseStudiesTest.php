<?php

namespace Tests\Feature\Seo;

use Tests\Support\CrawlsPages;
use Tests\TestCase;

/** The V2 case-study sections come from config/case_studies.php: PDFs in public/case-studies/v2/, covers = designed images in v2/cover/. */
class CaseStudiesTest extends TestCase
{
    use CrawlsPages;

    public function test_every_listed_case_study_has_its_pdf_and_a_1080x605_cover(): void
    {
        $list = config('case_studies');
        $this->assertCount(16, $list);
        foreach ($list as $c) {
            $pdf = public_path('case-studies/v2/' . $c['pdf'] . '.pdf');
            $cover = public_path('case-studies/v2/cover/' . $c['cover'] . '.jpg');
            $this->assertFileExists($pdf, $c['name'] . ' pdf');
            $this->assertStringStartsWith('%PDF', file_get_contents($pdf, false, null, 0, 5), $c['name'] . ' is a real PDF');
            $this->assertFileExists($cover, $c['name'] . ' cover');
            [$w, $h, $type] = getimagesize($cover);
            $this->assertSame([1080, 605, IMAGETYPE_JPEG], [$w, $h, $type], $c['name'] . ' cover size');
            $this->assertLessThan(150 * 1024, filesize($cover), $c['name'] . ' cover stays light');
            foreach (['' => [1080, 605], '-w960' => [960, 538], '-w480' => [480, 269]] as $suffix => $size) {
                $webp = public_path('case-studies/v2/cover/' . $c['cover'] . $suffix . '.webp');
                $this->assertFileExists($webp, $c['name'] . " webp$suffix");
                [$w, $h, $type] = getimagesize($webp);
                $this->assertSame([...$size, IMAGETYPE_WEBP], [$w, $h, $type], $c['name'] . " webp$suffix size");
                $this->assertLessThan(80 * 1024, filesize($webp), $c['name'] . " webp$suffix stays light");
            }
        }
    }

    public function test_every_pdf_in_the_folder_is_listed_and_names_are_unique(): void
    {
        $listed = array_map(fn ($c) => $c['pdf'] . '.pdf', config('case_studies'));
        $onDisk = array_map('basename', glob(public_path('case-studies/v2/*.pdf')));
        sort($listed); sort($onDisk);
        $this->assertSame($onDisk, $listed, 'a PDF was added or removed without updating config/case_studies.php');
        $covers = array_column(config('case_studies'), 'cover', 'name');
        $this->assertSame('lenskart', $covers['Lenskart KSA']);
        $this->assertSame('lenskart', $covers['Lenskart UAE']);
        $this->assertCount(count($listed) - 1, array_unique($covers), 'one cover per case study; only the two Lenskart ones share');
    }

    public function test_helper_builds_encoded_urls_on_the_current_host(): void
    {
        $this->get(self::STAGING . '/');
        $first = v2_case_studies()[0];
        $this->assertSame('Nike', $first['name']);
        $this->assertSame('https://staging.datamysite.com/public/case-studies/v2/NIKE%20FINAL%20CASE%20STUDY.pdf', $first['pdf_url']);
        $this->assertSame('public/case-studies/v2/cover/nike.jpg', $first['cover']);
        $this->assertSame('Nike × DMS case study cover', $first['alt']);
    }

    public function test_home_service_pages_case_studies_page_and_about_list_the_new_case_studies(): void
    {
        $names = array_column(config('case_studies'), 'name');
        foreach (['/', '/client-case-studies', '/about-us', '/seo-services', '/outdoor-advertising'] as $path) {
            $html = $this->html($path);
            preg_match_all('#href="(https://datamysite\.com/public/case-studies/v2/[^"]+\.pdf)"#', $html, $m);
            $this->assertCount(16, array_unique($m[1]), "$path links all 16 PDFs");
            preg_match_all('#case-studies/v2/cover/([a-z0-9-]+?)(?:-w\d+)?\.(?:jpg|webp)#', $html, $c);
            $this->assertEqualsCanonicalizing(array_values(array_unique(array_column(config('case_studies'), 'cover'))), array_values(array_unique($c[1])), "$path shows all the covers");
            $this->assertStringNotContainsString('public/case-studies/cover/', $html, "$path no longer uses the old covers");
            $this->assertStringNotContainsString('/public/case-studies/bnw.pdf', $html, "$path no longer links the old PDFs");
        }
    }

    public function test_cached_service_sections_expire_when_the_case_study_list_changes(): void
    {
        $view = file_get_contents(resource_path('views/web/services-v2/page.blade.php'));
        $this->assertMatchesRegularExpression('#\$sv2Sig = md5\(json_encode\(\[.*?config\(\'case_studies\'\).*?\]\)\);#s', $view);
    }

    public function test_case_study_images_have_descriptive_alt_text_and_sizes(): void
    {
        foreach (['/', '/client-case-studies', '/about-us'] as $path) {
            $html = $this->html($path);
            preg_match_all('#<img\b[^>]*case-studies/v2/cover/[^>]*>#', $html, $m);
            $this->assertCount(16, $m[0], $path);
            foreach ($m[0] as $img) {
                $this->assertMatchesRegularExpression('#alt="[^"]+ × DMS case study cover"#u', $img, "$path alt");
                $this->assertMatchesRegularExpression('#width="1080" height="605"#', $img, "$path has intrinsic size (no layout shift)");
            }
        }
    }

    public function test_pdf_links_open_in_a_new_tab_safely_and_names_are_shown(): void
    {
        $html = $this->html('/client-case-studies');
        $this->assertSame(16, preg_match_all('#<a href="https://datamysite\.com/public/case-studies/v2/[^"]+\.pdf" target="_blank" rel="noopener"#', $html));
        foreach (config('case_studies') as $c) {
            $this->assertStringContainsString('<span>' . e($c['name']) . '</span> x <span>DMS</span>', $html, $c['name']);
        }
    }
}
