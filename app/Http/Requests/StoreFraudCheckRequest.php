<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFraudCheckRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'                 => 'required|in:identity,email,phone,ip,transaction',
            'input_value'          => 'nullable|string|max:500',
            'customer_name'        => 'nullable|string|max:255',
            'customer_email'       => 'nullable|email|max:255',
            'customer_phone'       => 'nullable|string|max:30',
            'ip_address'           => 'nullable|ip',
            'transaction_amount'   => 'nullable|numeric|min:0',
            'transaction_currency' => 'nullable|string|size:3',
            'device_type'          => 'nullable|string|max:50',
            'browser'              => 'nullable|string|max:100',
            'os'                   => 'nullable|string|max:100',
            'notes'                => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Check type is required.',
            'type.in'       => 'Invalid check type selected.',
            'ip_address.ip' => 'Please enter a valid IP address.',
        ];
    }
}
