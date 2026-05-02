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
            'ph' => 'required|numeric',
            'ketinggian_air' => 'required|numeric',
            'suhu_air' => 'required|numeric',
            'salinitas' => 'required|numeric',
            'rssi' => 'required|numeric',
            'device_timestamp' => 'required|date',
        ];
    }
}
