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
        ];
    }
}
