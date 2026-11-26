<?php

namespace Tests\Feature\Seo;

use Tests\TestCase;

/** /llms.txt (PageSpeed "Agentic Browsing" audit). It is a static file served by Apache, so these tests read it from disk. */
class LlmsTxtTest extends TestCase
{
    public function test_file_name_is_exactly_lowercase(): void
    {
        // scandir() returns the real spelling; is_file() would also accept "LLMs.txt" on Windows, while the Linux server answers 404 for it
        $this->assertContains('llms.txt', scandir(base_path()));
    }

    public function test_content_meets_the_lighthouse_rules(): void
    {
        $text = file_get_contents(base_path('llms.txt'));
        $this->assertMatchesRegularExpression('/^\s*#\s+.+/m', $text, 'an H1 header');
        $this->assertMatchesRegularExpression('/\[.+\]\(.+\)/', $text, 'at least one Markdown link: [text](url)');
        $this->assertGreaterThanOrEqual(50, strlen($text));
    }

    public function test_the_old_address_still_answers(): void
    {
        $this->assertStringContainsString('RewriteRule ^LLMs\.txt$ llms.txt [L]', file_get_contents(base_path('.htaccess')));
    }
}
