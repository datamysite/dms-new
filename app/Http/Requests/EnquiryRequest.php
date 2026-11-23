<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnquiryRequest extends FormRequest
{
    /**
     * Words / phrases that mark an enquiry as unrelated (job seekers, loans, adult services, vendor pitches ...).
     * Matched as WHOLE words / phrases, case-insensitive: "free" in "carefree" or "social media" in a genuine enquiry for
     * social media marketing no longer blocks a real lead. Terms that describe our own services (social media, website
     * development, SEO / website audit, app development, Google Maps, paid promotion, brand collaboration ...) are NOT blocked.
     */
    public const BLOCKED_KEYWORDS = [
        // jobs / careers
        'CV', 'Resume', 'Job application', 'Employment request', 'Work visa', 'Internship', 'Career opportunity', 'Cover letter',
        'Hiring inquiry', 'Apply for a job', 'Fresh graduate', 'HR department', 'Recruitment request', 'Open positions',
        'Work from home', 'Part-time job', 'Full-time job',
        // loans / finance
        'Loan', 'Loans', 'Mortgage', 'Credit', 'Salary advance', 'Debt consolidation', 'Personal loan', 'Home loan', 'Car loan',
        'Finance request', 'Crypto lending', 'Banking assistance',
        // adult services
        'Escort', 'Massage service', 'Companion', 'Call girl', 'Call girls', 'girl', 'girls', 'Sensual service',
        'Private entertainment', 'Adult service',
        // visas / scams
        'Fake passport', 'Visa assistance', 'Immigration', 'Lottery',
        // vendor pitches / spam
        'Dropshipping supplier', 'Import/export inquiry', 'Cheap wholesale supply', 'Resell opportunity', 'Sponsored post',
        'Instagram growth service', 'Domain selling', 'Guest post', 'website traffic', 'Send bulk SMS', 'SEO backlinking',
        'verification badge', 'Telegram', 'Link exchange', 'Guest blogging', 'backlink request', 'website listing',
        'WordPress design', 'AI chatbot', 'Plugin installation', 'migration service', 'website issues', 'Malware',
        'Instagram likes', 'Get verified', 'Facebook account', 'TikTok growth', 'Business listing', 'Logo design', 'Graphic design',
    ];

    public function rules(): array
    {
        return [
            'email' => 'required|email:rfc,dns',
            'name' => ['required', 'regex:/^[a-zA-Z\s]+$/', 'min:3', 'max:50'],
            'phone' => ['required', 'regex:/^\+[0-9]{7,14}$/'],
            'description' => ['required', 'string', function ($attribute, $value, $fail) {
                foreach (self::BLOCKED_KEYWORDS as $keyword) {
                    // whole word / phrase: not preceded or followed by another letter or digit
                    $pattern = '/(?<![\p{L}\p{N}])' . preg_quote($keyword, '/') . '(?![\p{L}\p{N}])/iu';
                    if (preg_match($pattern, (string) $value)) {
                        $fail("Your message looks like an unrelated request (\"$keyword\"). Please tell us about your marketing or advertising enquiry.");
                        return;
                    }
                }
            }],
        ];
    }
}
