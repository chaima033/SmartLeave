<?php

namespace App\Http\Requests\Chatbot;

use Illuminate\Foundation\Http\FormRequest;

class ChatbotRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (! $this->has('prompt') && $this->has('message'))
        {
            $this->merge(['prompt' => $this->input('message')]);
        }

        if (! $this->has('mode'))
        {
            $this->merge(['mode' => 'candidate']);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prompt' => ['required', 'string', 'min:3'],
            'mode' => ['required', 'in:candidate,recruiter'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
