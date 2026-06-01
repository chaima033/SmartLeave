<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->user()?->role === 'recruiter')
        {
            return [
                'name' => ['required', 'string', 'max:255'],
                'phone' => ['nullable', 'string', 'max:30'],
                'location' => ['nullable', 'string', 'max:255'],
                'company_name' => ['required', 'string', 'max:255'],
                'company_industry' => ['nullable', 'string', 'max:255'],
                'company_website' => ['nullable', 'url', 'max:255'],
                'company_size' => ['nullable', 'string', 'max:255'],
                'company_description' => ['nullable', 'string'],
                'bio' => ['nullable', 'string'],
            ];
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'headline' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'desired_role' => ['nullable', 'string', 'max:255'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:50'],
            'skills' => ['nullable', 'string'],
            'salary_expectation' => ['nullable', 'string', 'max:255'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
        ];
    }
}
