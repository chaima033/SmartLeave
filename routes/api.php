<?php

use App\Http\Controllers\AiAssistantController;
use Illuminate\Support\Facades\Route;

Route::post('/chatbot', [AiAssistantController::class, 'askApi'])->name('api.chatbot');
