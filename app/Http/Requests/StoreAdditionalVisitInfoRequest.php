<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdditionalVisitInfoRequest extends FormRequest
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
            'userAgent' => 'required|string',
            'language' => 'required|string',
            'platform' => 'required|string',
            'screenResolution' => 'required|string',
            'colorDepth' => 'required|integer',
            'timezone' => 'required|string',
            'plugins' => 'required|array',
            'cookiesEnabled' => 'required|boolean',
            'hardwareConcurrency' => 'required|integer',
            'onlineStatus' => 'required|boolean',
            'viewportSize' => 'required|string',
            'canvasFingerprint' => 'required|string',
            'installedFonts' => 'required|array',
            'browserName' => 'required|string',
            'browserVersion' => 'required|string',
            'windowSize' => 'required|array',
            'localStorageAvailable' => 'required|boolean',
            'sessionStorageAvailable' => 'required|boolean',
            'cssProperties' => 'required|array',
        ];
    }
}
