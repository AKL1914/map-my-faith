<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
        $areaGroups = config('app.area_groups');
        $validAreas = implode(',', array_keys($areaGroups));
        $validCfos = ['BUKLOD', 'KADIWA', 'BINHI'];

        return [
            'area' => ['nullable', 'in:' . $validAreas],
            'cfo' => ['nullable', 'in:' . implode(',', $validCfos)],
            'name' => ['required', 'string', 'max:255'],
            'group' => [
                'nullable',
                function ($attribute, $value, $fail) use ($areaGroups) {
                    $area = $this->input('area');
                    if ($area && isset($areaGroups[$area])) {
                        if (!in_array($value, $areaGroups[$area])) {
                            $fail("The selected group is invalid for the chosen area.");
                        }
                    }
                }
            ],
        ];
    }
}
