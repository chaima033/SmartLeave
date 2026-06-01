<?php

namespace App\Http\Requests\Application;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:submitted,reviewing,shortlisted,rejected,hired'],
            'recruiter_feedback' => ['nullable', 'string'],
        ];
    }
}
