<?php

namespace App\Http\Requests\Application;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'resume_id' => ['nullable', 'integer', 'exists:resumes,id'],
            'cover_letter' => ['nullable', 'string'],
            'candidate_notes' => ['nullable', 'string'],
        ];
    }
}
