<?php

namespace App\Http\Requests\Resume;

use Illuminate\Foundation\Http\FormRequest;

abstract class ResumeRequest extends FormRequest
{
    protected const RESUME_RULES = [
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

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return self::RESUME_RULES;
    }
}
