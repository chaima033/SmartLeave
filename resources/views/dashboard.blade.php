@extends('layouts.app')

@section('content')
    <div class="mb-8 grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div>
            <span class="badge">{{ auth()->user()->role === 'recruiter' ? 'Espace recruteur' : 'Espace candidat' }}</span>
            <h1 class="section-title mt-4">Bonjour {{ auth()->user()->name }}</h1>
            <p class="section-subtitle">Un tableau de bord clair pour piloter vos offres, vos candidatures et votre talent pipeline.</p>
        </div>

        <div class="space-y-3 rounded-[1.75rem] border border-slate-200/70 bg-slate-50/90 p-5 shadow-sm">
            <p class="text-sm font-semibold text-slate-900">Actions rapides</p>
            <div class="grid gap-3 sm:grid-cols-2">
                <a class="btn-secondary w-full" href="{{ route('jobs.index') }}">Voir les offres</a>
                <a class="btn-primary w-full" href="{{ route('assistant.index') }}">Assistant IA</a>
                @if (auth()->user()->role === 'candidate')
                    <a class="btn-secondary w-full" href="{{ route('resumes.index') }}">Mes CV</a>
                @else
                    <a class="btn-secondary w-full" href="{{ route('recruiter.resumes.index') }}">CV candidats</a>
                @endif
            </div>
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-3">
        @if ($mode === 'recruiter')
            <div class="stat-card">
                <p class="text-sm text-slate-500">Offres publiées</p>
                <p class="mt-4 text-4xl font-semibold text-slate-950">{{ $offersCount }}</p>
            </div>
            <div class="stat-card">
                <p class="text-sm text-slate-500">Candidatures reçues</p>
                <p class="mt-4 text-4xl font-semibold text-slate-950">{{ $applicationsCount }}</p>
            </div>
            <div class="stat-card">
                <p class="text-sm text-slate-500">Assistant recrutement</p>
                <p class="mt-4 text-4xl font-semibold text-slate-950">IA</p>
            </div>
        @else
            <div class="stat-card">
                <p class="text-sm text-slate-500">CV créés</p>
                <p class="mt-4 text-4xl font-semibold text-slate-950">{{ $resumesCount }}</p>
            </div>
            <div class="stat-card">
                <p class="text-sm text-slate-500">Candidatures envoyées</p>
                <p class="mt-4 text-4xl font-semibold text-slate-950">{{ $applicationsCount }}</p>
            </div>
            <div class="stat-card">
                <p class="text-sm text-slate-500">Offres actives</p>
                <p class="mt-4 text-4xl font-semibold text-slate-950">{{ $jobsCount }}</p>
            </div>
        @endif
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        @if ($mode === 'recruiter')
            <div class="card-lg p-6">
                <div class="mb-5 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-950">Dernières offres</h2>
                        <p class="text-sm text-slate-500">Restez à jour avec vos publications récentes.</p>
                    </div>
                    <a class="btn-ghost" href="{{ route('recruiter.jobs.index') }}">Voir tout</a>
                </div>
                <div class="space-y-4">
                    @forelse ($offers as $offer)
                        <a class="block rounded-[1.75rem] border border-slate-200 p-5 transition hover:border-cyan-300 hover:bg-cyan-50/60" href="{{ route('recruiter.jobs.edit', $offer) }}">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-lg font-semibold text-slate-950">{{ $offer->title }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $offer->location ?? 'Remote' }} · {{ ucfirst($offer->status) }}</p>
                                </div>
                                <span class="info-chip">{{ $offer->contract_type ?? 'Contrat' }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-[1.75rem] border border-dashed border-slate-200 p-6 text-center text-slate-500">Aucune offre pour le moment.</div>
                    @endforelse
                </div>
            </div>
            <div class="card-lg p-6">
                <div class="mb-5 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-950">Dernières candidatures</h2>
                        <p class="text-sm text-slate-500">Suivez rapidement les candidats récents.</p>
                    </div>
                    <a class="btn-ghost" href="{{ route('recruiter.applications.index') }}">Voir tout</a>
                </div>
                <div class="space-y-4">
                    @forelse ($applications as $application)
                        <div class="rounded-[1.75rem] border border-slate-200 p-5">
                            <p class="font-semibold text-slate-950">{{ $application->candidate->name }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $application->jobOffer->title }}</p>
                            <span class="status-pill status-pill--{{ $application->status }} mt-4 inline-block">{{ ucfirst($application->status) }}</span>
                        </div>
                    @empty
                        <div class="rounded-[1.75rem] border border-dashed border-slate-200 p-6 text-center text-slate-500">Aucune candidature pour le moment.</div>
                    @endforelse
                </div>
            </div>
        @else
            <div class="card-lg p-6">
                <div class="mb-5 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-950">Mon CV</h2>
                        <p class="text-sm text-slate-500">Ajoutez un CV pour postuler plus efficacement.</p>
                    </div>
                    <a class="btn-primary" href="{{ route('resumes.create') }}">Nouveau CV</a>
                </div>
                <p class="text-sm text-slate-600">Un CV bien renseigné améliore vos chances et enrichit l'Assistant IA.</p>
            </div>
            <div class="card-lg p-6">
                <div class="mb-5 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-950">Dernières offres</h2>
                        <p class="text-sm text-slate-500">Découvrez les nouvelles opportunités adaptées.</p>
                    </div>
                    <a class="btn-ghost" href="{{ route('jobs.index') }}">Voir tout</a>
                </div>
                <div class="space-y-4">
                    @forelse ($jobs as $job)
                        <a class="block rounded-[1.75rem] border border-slate-200 p-5 transition hover:border-cyan-300 hover:bg-cyan-50/60" href="{{ route('jobs.show', $job) }}">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-lg font-semibold text-slate-950">{{ $job->title }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $job->companyProfile?->company_name ?? $job->recruiter->company_name ?? $job->recruiter->name }}</p>
                                </div>
                                <span class="info-chip">{{ $job->location ?? 'Remote' }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-[1.75rem] border border-dashed border-slate-200 p-6 text-center text-slate-500">Aucune offre disponible.</div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
@endsection
