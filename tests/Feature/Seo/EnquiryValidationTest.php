<?php

namespace Tests\Feature\Seo;

use App\Helpers\Mailer;
use App\Http\Requests\EnquiryRequest;
use App\Models\Enquiry;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/** Audit item: forms must accept real enquiries (the keyword filter used to reject "free" / "social media") and never break on mail errors. */
class EnquiryValidationTest extends TestCase
{
    use DatabaseTransactions;

    private function descriptionPasses(string $text): bool
    {
        $rule = (new EnquiryRequest)->rules()['description'];
        return Validator::make(['description' => $text], ['description' => $rule])->passes();
    }

    public function test_genuine_marketing_enquiries_are_accepted(): void
    {
        foreach ([
            'I want a free consultation about social media marketing',
            'We need website development and an SEO audit for our clinic in Dubai',
            'Interested in billboards and Google Maps ads for a carefree launch',
            'Looking for paid promotion and brand collaboration with influencers',
            'creditors and resumed work are fine words, they are not blocked',
        ] as $text) {
            $this->assertTrue($this->descriptionPasses($text), "should be accepted: $text");
        }
    }

    public function test_unrelated_or_spam_requests_are_still_rejected(): void
    {
        foreach ([
            'Looking for a loan to start a business',
            'Please find my CV attached for the internship',
            'Need a call girl',
            'We offer guest post and SEO backlinking',
            'Join our Telegram group',
            'WORK FROM HOME opportunity',
        ] as $text) {
            $this->assertFalse($this->descriptionPasses($text), "should be rejected: $text");
        }
    }

    public function test_the_rejection_message_is_readable(): void
    {
        $rule = (new EnquiryRequest)->rules()['description'];
        $v = Validator::make(['description' => 'I need a loan'], ['description' => $rule]);
        $this->assertTrue($v->fails());
        $this->assertStringContainsString('unrelated request', $v->errors()->first('description'));
    }

    public function test_a_mail_failure_does_not_break_the_submit(): void
    {
        Mail::shouldReceive('send')->andThrow(new \RuntimeException('SMTP is down: key=SECRET'));
        $this->assertFalse(Mailer::sendMail('Subject', ['a@example.com'], 'DMS', 'web.emails.response', ['name' => 'x']), 'returns false instead of throwing');
    }

    public function test_the_hero_form_saves_the_lead_and_answers_success(): void
    {
        if (!checkdnsrr('gmail.com', 'MX')) $this->markTestSkipped('needs DNS: the email rule checks the domain');
        Mail::fake();
        $before = Enquiry::count();

        $response = $this->postJson('https://datamysite.com/enquiry', [
            'name' => 'Test Visitor',
            'email' => 'visitor.test@gmail.com',
            'phone' => '+971500000000',
            'description' => 'I want a free consultation about social media marketing',
            'service' => 'SEO Services',
        ]);

        $response->assertOk()->assertJson(['success' => 'success']);
        $this->assertSame($before + 1, Enquiry::count(), 'one enquiry saved');
        $this->assertSame('SEO Services', Enquiry::latest('id')->first()->service);
    }

    public function test_the_hero_form_rejects_a_bad_message_with_a_readable_error(): void
    {
        if (!checkdnsrr('gmail.com', 'MX')) $this->markTestSkipped('needs DNS: the email rule checks the domain');
        Mail::fake();
        $before = Enquiry::count();

        $response = $this->postJson('https://datamysite.com/enquiry', [
            'name' => 'Test Visitor', 'email' => 'visitor.test@gmail.com', 'phone' => '+971500000000',
            'description' => 'I am looking for a loan',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['description']);
        $this->assertSame($before, Enquiry::count(), 'nothing saved');
    }

    public function test_the_help_popup_saves_the_service_and_no_longer_says_newsletter(): void
    {
        if (!checkdnsrr('gmail.com', 'MX')) $this->markTestSkipped('needs DNS: the email rule checks the domain');
        Mail::fake();

        $response = $this->postJson('https://datamysite.com/enquiryHelp', [
            'name' => 'Test Visitor', 'email' => 'visitor.test@gmail.com', 'phone' => '+971500000000',
            'website_link' => 'https://example.com', 'service' => 'SEO Services',
        ]);

        $response->assertOk()->assertJson(['success' => 'success']);
        $this->assertStringNotContainsStringIgnoringCase('newsletter', $response->json('message'));
        $this->assertSame('SEO Services', Enquiry::latest('id')->first()->service);
    }
}
