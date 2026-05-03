<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MonitoringRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kolam_id' => 'required|exists:kolams,id',
            'ph' => 'required|numeric|between:0,14',
            'ketinggian_air' => 'required|numeric|min:0',
            'suhu_air' => 'required|numeric|between:-50,100',
            'salinitas' => 'required|numeric|min:0',
            'rssi' => 'required|numeric|between:-150,0',
            'device_timestamp' => 'required|date',
        ];
    }
}
