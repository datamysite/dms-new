<?php

namespace Tests\Feature\Seo;

use Tests\Support\CrawlsPages;
use Tests\TestCase;

/** The V2 case-study sections come from config/case_studies.php: PDFs in public/case-studies/v2/, covers = first slide of each PDF. */
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
        }
    }

    public function test_every_pdf_in_the_folder_is_listed_and_names_are_unique(): void
    {
        $listed = array_map(fn ($c) => $c['pdf'] . '.pdf', config('case_studies'));
        $onDisk = array_map('basename', glob(public_path('case-studies/v2/*.pdf')));
        sort($listed); sort($onDisk);
        $this->assertSame($onDisk, $listed, 'a PDF was added or removed without updating config/case_studies.php');
        $this->assertSame(count($listed), count(array_unique(array_column(config('case_studies'), 'cover'))), 'one cover per case study');
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
            preg_match_all('#case-studies/v2/cover/([a-z0-9-]+)\.(?:jpg|webp)#', $html, $c);
            $this->assertCount(16, array_unique($c[1]), "$path shows all 16 covers");
            $this->assertStringNotContainsString('public/case-studies/cover/', $html, "$path no longer uses the old covers");
            $this->assertStringNotContainsString('/public/case-studies/bnw.pdf', $html, "$path no longer links the old PDFs");
        }
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
