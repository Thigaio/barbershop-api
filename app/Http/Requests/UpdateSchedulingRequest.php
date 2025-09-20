<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchedulingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date'    => 'sometimes|required|date',
            'time'    => 'sometimes|required',
            'service' => 'sometimes|required|string|max:255',
        ];
    }
}
