<?php

namespace App\Http\Requests\Device;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip|unique:devices,ip_address,' . $this->route('device')->id,
            'device_type_id' => 'required|exists:device_types,id',
            'snmp_community' => 'nullable|string|max:255',
            'snmp_version' => 'nullable|in:v1,v2c,v3',
            'snmp_port' => 'nullable|integer|min:1|max:65535',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}
