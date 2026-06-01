<?php

namespace Tests\Unit;

use App\Http\Requests\Chatbot\ChatbotRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ChatbotRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_prepare_for_validation_sets_prompt_from_message(): void
    {
        $request = new class extends ChatbotRequest {
            public function callPrepareForValidation(): void
            {
                $this->prepareForValidation();
            }
        };

        $request->initialize(['message' => 'Bonjour']);

        $request->callPrepareForValidation();

        $this->assertSame('Bonjour', $request->input('prompt'));
    }

    public function test_prepare_for_validation_sets_default_mode_when_missing(): void
    {
        $request = new class extends ChatbotRequest {
            public function callPrepareForValidation(): void
            {
                $this->prepareForValidation();
            }
        };

        $request->initialize(['prompt' => 'Hello']);

        $request->callPrepareForValidation();

        $this->assertSame('candidate', $request->input('mode'));
    }
}
