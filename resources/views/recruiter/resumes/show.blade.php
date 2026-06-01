@extends('layouts.app')

@section('content')
    <div class="grid gap-8 xl:grid-cols-[1fr_1.15fr]">
        <section class="card-lg p-8">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <span class="badge">Candidat</span>
                    <h1 class="section-title mt-4">{{ $candidate->name }}</h1>
                    <p class="mt-3 text-sm text-slate-500">{{ $candidate->candidateProfile?->headline ?? 'Profil candidat' }}</p>
                </div>
                <span class="status-pill status-pill--submitted">Profil candidat</span>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-2 text-sm text-slate-600">
                <div class="space-y-4 rounded-[1.75rem] border border-slate-200/70 bg-slate-50 p-6">
                    <p><span class="font-semibold text-slate-900">Email :</span> {{ $candidate->email }}</p>
                    <p><span class="font-semibold text-slate-900">Téléphone :</span> {{ $candidate->candidateProfile?->phone ?? $candidate->phone ?? 'N/A' }}</p>
                    <p><span class="font-semibold text-slate-900">Localisation :</span> {{ $candidate->candidateProfile?->location ?? 'N/A' }}</p>
                </div>
                <div class="space-y-4 rounded-[1.75rem] border border-slate-200/70 bg-slate-50 p-6">
                    <p><span class="font-semibold text-slate-900">Rôle recherché :</span> {{ $candidate->candidateProfile?->desired_role ?? 'N/A' }}</p>
                    <p><span class="font-semibold text-slate-900">Expérience :</span> {{ $candidate->candidateProfile?->experience_years ?? 'N/A' }} ans</p>
                    <p><span class="font-semibold text-slate-900">Portfolio :</span> {{ $candidate->candidateProfile?->portfolio_url ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="mt-8 rounded-[1.75rem] border border-slate-200/70 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-950">Compétences</h2>
                <p class="mt-3 text-sm text-slate-600">{{ implode(', ', $candidate->candidateProfile?->skills ?? []) ?: 'Aucune compétence renseignée' }}</p>
            </div>
        </section>

        <section class="space-y-6">
            @forelse ($candidate->resumes as $resume)
                <article class="card p-6 transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_24px_60px_rgba(15,23,42,0.08)]">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-950">{{ $resume->title }}</h2>
                            <p class="mt-2 text-sm text-slate-500">{{ $resume->is_primary ? 'Principal' : 'Secondaire' }}</p>
                        </div>
                        <span class="badge">CV</span>
                    </div>

                    <div class="mt-6 space-y-4 text-sm text-slate-600">
                        <p><span class="font-semibold text-slate-900">Résumé :</span> {{ $resume->summary ?? 'N/A' }}</p>
                        <p><span class="font-semibold text-slate-900">Expérience :</span> {{ $resume->experience ?? 'N/A' }}</p>
                        <p><span class="font-semibold text-slate-900">Éducation :</span> {{ $resume->education ?? 'N/A' }}</p>
                        <p><span class="font-semibold text-slate-900">Compétences :</span> {{ implode(', ', $resume->skills ?? []) ?: 'N/A' }}</p>
                        <p><span class="font-semibold text-slate-900">Projets :</span> {{ $resume->projects ?? 'N/A' }}</p>
                        <p><span class="font-semibold text-slate-900">Certifications :</span> {{ $resume->certifications ?? 'N/A' }}</p>
                        <p><span class="font-semibold text-slate-900">Langues :</span> {{ $resume->languages ?? 'N/A' }}</p>
                    </div>
                </article>
            @empty
                <div class="card p-10 text-center text-slate-500">Ce candidat n'a pas encore de CV.</div>
            @endforelse
        </section>
    </div>
@endsection
