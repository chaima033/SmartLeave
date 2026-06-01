@extends('layouts.app')

@section('content')
    <div class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
        <section class="surface p-6 lg:p-8">
            <div class="badge">Assistant IA</div>
            <h1 class="section-title mt-4">Aide emploi et recrutement</h1>
            <p class="mt-2 text-slate-600">Posez une question sur votre CV, vos offres ou votre selection.</p>

            <form method="POST" action="{{ route('assistant.ask') }}" class="mt-8 space-y-4" data-ai-chat-form>
                @csrf
                <input type="hidden" name="mode" value="{{ auth()->user()->role }}">
                <div>
                    <label class="label">Votre demande</label>
                    <textarea class="input-field min-h-40" name="prompt" data-ai-prompt placeholder="Ex: Revois mon CV pour un poste Laravel"></textarea>
                </div>
                <button class="btn-primary" data-ai-submit>Demander à l assistant</button>
            </form>

            <p class="mt-6 text-sm text-slate-500">Le service utilise le endpoint Gemini configure dans l environnement.</p>
        </section>

        <section class="surface p-6 lg:p-8">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold">Historique</h2>
                <span class="badge">{{ $history->count() }} messages</span>
            </div>

            <div class="mb-4 rounded-3xl border border-slate-200 bg-orange-50 p-4 text-sm text-slate-700" data-ai-output>
                <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Reponse instantanee</div>
                <p class="mt-2 min-h-24 whitespace-pre-line" data-ai-answer>Posez votre premiere question pour obtenir un conseil cible.</p>
            </div>

            <div class="space-y-4">
                <template data-ai-template>
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                        <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Reponse</div>
                        <p class="mt-2 whitespace-pre-line" data-role="assistant"></p>
                    </div>
                </template>

                @foreach ($history as $message)
                    <div class="rounded-3xl border border-slate-200 bg-white p-4 text-sm text-slate-700">
                        <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Votre question</div>
                        <p class="mt-2 whitespace-pre-line">{{ $message->prompt }}</p>
                        <div class="mt-4 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Reponse</div>
                        <p class="mt-2 whitespace-pre-line">{{ $message->response }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
@endsection
