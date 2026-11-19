<?php
// File: app/Http/Requests/StoreBlogRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogRequest extends FormRequest
{
    // Authorisation is handled by the VerifyApiToken middleware on the route.
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'heading'           => ['required', 'string', 'max:255'],
            'slug'              => ['nullable', 'string', 'max:255', 'unique:blogs,slug'],
            'description'       => ['required', 'string'],            // main body (HTML or Markdown)
            'short_description' => ['nullable', 'string'],            // used as meta description / excerpt
            'faq_schema'        => ['nullable'],                      // array or JSON string (JSON-LD)
            'banner'            => ['nullable', 'string', 'max:255'],
            'banner_alt'        => ['nullable', 'string', 'max:500'],
            'category_id'       => ['nullable', 'integer'],
            'read_time'         => ['nullable', 'string', 'max:150'],
            'status'            => ['nullable', 'integer', 'in:0,1'],
            'author_id'         => ['nullable', 'integer'],
        ];
    }
}
