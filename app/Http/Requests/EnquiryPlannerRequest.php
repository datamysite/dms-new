<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * Lead form of the service pages ("Speak With Our Planning Team"): the normal enquiry rules (name, email, phone,
 * blocked-keyword check on the message) plus budget range, and preferred dates.
 */
class EnquiryPlannerRequest extends EnquiryRequest
{
    /** Same values as the dropdown in the service section (DB content). */
    public const BUDGETS = [
        'Under AED 20,000', 'AED 20,000 - 50,000', 'AED 50,000 - 100,000',
        'AED 100,000 - 250,000', 'AED 250,000 and above', 'Not sure yet',
    ];

    public function rules(): array
    {
        return parent::rules() + [
            'budget' => ['nullable', 'string', Rule::in(self::BUDGETS)], // Rule::in, not "in:a,b": the values contain commas
            'dates' => ['nullable', 'string', 'max:120'],
        ];
    }
}
