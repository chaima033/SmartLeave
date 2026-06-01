@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <span class="badge">CV candidats</span>
        <h1 class="section-title mt-4">Consultation des CV</h1>
        <p class="section-subtitle">Parcourez les candidats, leurs compétences et leurs expériences en un clin d'œil.</p>
    </div>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($candidates as $candidate)
            <a class="card p-6 transition duration-200 hover:-translate-y-1 hover:shadow-[0_24px_70px_rgba(15,23,42,0.08)]" href="{{ route('recruiter.resumes.show', $candidate) }}">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-950">{{ $candidate->name }}</h2>
                        <p class="mt-2 text-sm text-slate-500">{{ $candidate->candidateProfile?->headline ?? 'Candidat' }}</p>
                    </div>
                    <span class="badge">CV</span>
                </div>
                <div class="mt-5 space-y-3 text-sm text-slate-600">
                    <p><span class="font-semibold text-slate-900">Localisation :</span> {{ $candidate->candidateProfile?->location ?? 'N/A' }}</p>
                    <p><span class="font-semibold text-slate-900">CV :</span> {{ $candidate->resumes->count() }}</p>
                </div>
            </a>
        @empty
            <div class="card p-10 text-center text-slate-500">Aucun candidat trouvé.</div>
        @endforelse
    </div>

    <div class="mt-8">{{ $candidates->links() }}</div>
@endsection
