<?php

namespace App\Http\Requests\Resume;

use Illuminate\Foundation\Http\FormRequest;

abstract class ResumeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'experience' => ['nullable', 'string'],
            'education' => ['nullable', 'string'],
            'skills' => ['nullable', 'string'],
            'projects' => ['nullable', 'string'],
            'certifications' => ['nullable', 'string'],
            'languages' => ['nullable', 'string'],
            'is_primary' => ['nullable', 'boolean'],
        ];
    }
}
