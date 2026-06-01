@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <span class="badge">Recrutement</span>
        <h1 class="section-title mt-4">Candidatures reçues</h1>
        <p class="section-subtitle">Évaluez rapidement chaque profil et mettez à jour le statut en un clic.</p>
    </div>

    <div class="space-y-6">
        @forelse ($applications as $application)
            <article class="card p-6 transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_24px_60px_rgba(15,23,42,0.08)]">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-950">{{ $application->candidate->name }}</h2>
                        <p class="mt-2 text-sm text-slate-500">{{ $application->jobOffer->title }}</p>
                    </div>
                    <span class="status-pill status-pill--{{ $application->status }}">{{ ucfirst($application->status) }}</span>
                </div>

                <div class="mt-6 grid gap-6 lg:grid-cols-2">
                    <div>
                        <p class="font-semibold text-slate-900">Lettre de motivation</p>
                        <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $application->cover_letter ?? 'Aucune lettre' }}</p>
                    </div>
                    <div class="space-y-4">
                        <form method="POST" action="{{ route('recruiter.applications.update', $application) }}" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="label">Statut</label>
                                <select class="input-field" name="status">
                                    @foreach (['submitted', 'reviewing', 'shortlisted', 'rejected', 'hired'] as $status)
                                        <option value="{{ $status }}" @selected($application->status === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label">Feedback</label>
                                <textarea class="input-field min-h-28" name="recruiter_feedback" placeholder="Commentaires pour le candidat">{{ $application->recruiter_feedback }}</textarea>
                            </div>
                            <button class="btn-primary">Mettre à jour</button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="card p-10 text-center text-slate-500">Aucune candidature reçue.</div>
        @endforelse
    </div>

    <div class="mt-8">{{ $applications->links() }}</div>
@endsection
