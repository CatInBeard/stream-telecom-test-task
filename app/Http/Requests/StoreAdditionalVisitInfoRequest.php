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
            'userAgent' => 'sometimes|string',
            'language' => 'sometimes|string',
            'platform' => 'sometimes|string',
            'screenResolution' => 'sometimes|string',
            'colorDepth' => 'sometimes|integer',
            'timezone' => 'sometimes|string',
            'plugins' => 'sometimes|array',
            'cookiesEnabled' => 'sometimes|boolean',
            'hardwareConcurrency' => 'sometimes|integer',
            'onlineStatus' => 'sometimes|boolean',
            'viewportSize' => 'sometimes|string',
            'canvasFingerprint' => 'sometimes|string',
            'installedFonts' => 'sometimes|array',
            'browserName' => 'sometimes|string',
            'browserVersion' => 'sometimes|string',
            'windowSize' => 'sometimes|array',
            'localStorageAvailable' => 'sometimes|boolean',
            'sessionStorageAvailable' => 'sometimes|boolean',
            'cssProperties' => 'sometimes|array',
        ];
    }
}
