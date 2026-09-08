<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PinStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'campaign_id' => 'required|exists:campaigns,id', // Ensure it's a valid campaign ID
            'notes' => 'nullable|max:50', // Optional, max length for notes
            'contact_name' => 'nullable|string|max:100',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => ['nullable', 'string', 'max:20', function ($attribute, $value, $fail) {
                $normalized = preg_replace('/[\s\-()]/', '', $value);
                if (! preg_match('/^(\+64|0)[2-9]\d{7,9}$/', $normalized)) {
                    $fail('The contact phone must be a valid NZ phone number.');
                }
            }],
        ];
    }
}
