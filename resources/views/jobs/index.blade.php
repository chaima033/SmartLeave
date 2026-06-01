@extends('layouts.app')

@section('content')
    <div class="mb-8 grid gap-6 lg:grid-cols-[1.3fr_0.7fr]">
        <div>
            <span class="badge">Offres d'emploi</span>
            <h1 class="section-title mt-4">Recherche d'opportunités</h1>
            <p class="section-subtitle">Découvrez les meilleures offres et postulez en quelques clics.</p>
        </div>
        <form method="GET" class="card p-5 shadow-sm">
            <label class="label">Recherche rapide</label>
            <div class="flex flex-col gap-3 sm:flex-row">
                <input class="input-field" name="search" value="{{ request('search') }}" placeholder="Titre, lieu, entreprise">
                <button class="btn-primary w-full sm:w-auto">Chercher</button>
            </div>
        </form>
    </div>

    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($jobs as $job)
            <article class="card p-6 transition duration-200 hover:-translate-y-1 hover:shadow-[0_28px_80px_rgba(15,23,42,0.08)]">
                <div class="flex flex-col gap-4">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-950">{{ $job->title }}</h2>
                            <p class="mt-2 text-sm text-slate-500">{{ $job->companyProfile?->company_name ?? $job->recruiter->company_name ?? $job->recruiter->name }}</p>
                        </div>
                        <span class="status-pill status-pill--published">{{ $job->status }}</span>
                    </div>

                    <p class="max-h-24 overflow-hidden text-sm leading-6 text-slate-600">{{ $job->description }}</p>

                    <div class="flex flex-wrap gap-2">
                        @foreach (array_slice($job->skills ?? [], 0, 4) as $skill)
                            <span class="info-chip">{{ $skill }}</span>
                        @endforeach
                    </div>

                    <div class="mt-4 flex flex-wrap items-center justify-between gap-4 text-sm text-slate-500">
                        <span>{{ $job->location ?? 'Remote' }}</span>
                        <span>{{ $job->contract_type ?? 'Contrat' }}</span>
                    </div>

                    <div class="mt-5 flex items-center justify-between">
                        <span class="text-sm text-slate-500">{{ $job->salary_min }} - {{ $job->salary_max }} {{ $job->currency }}</span>
                        <a class="btn-primary" href="{{ route('jobs.show', $job) }}">Voir</a>
                    </div>
                </div>
            </article>
        @empty
            <div class="card col-span-full p-10 text-center text-slate-500">Aucune offre trouvée.</div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $jobs->links() }}
    </div>
@endsection
