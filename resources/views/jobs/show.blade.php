@extends('layouts.app')

@section('content')
    <div class="grid gap-8 lg:grid-cols-[1.4fr_0.9fr]">
        <article class="card-lg p-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <span class="status-pill status-pill--{{ $job->status }}">{{ ucfirst($job->status) }}</span>
                    <h1 class="section-title mt-4">{{ $job->title }}</h1>
                    <p class="mt-2 text-sm text-slate-500">{{ $job->companyProfile?->company_name ?? $job->recruiter->company_name ?? $job->recruiter->name }}</p>
                </div>
                <div class="flex flex-wrap gap-2 text-sm text-slate-600">
                    <span class="info-chip">{{ $job->location ?? 'Remote' }}</span>
                    <span class="info-chip">{{ $job->contract_type ?? 'Contrat' }}</span>
                    <span class="info-chip">{{ $job->work_mode ?? 'Mode hybride' }}</span>
                </div>
            </div>

            <div class="mt-8 space-y-8 text-slate-700">
                <div class="space-y-3">
                    <h2 class="text-xl font-semibold text-slate-950">Description</h2>
                    <p class="text-sm leading-7">{{ $job->description }}</p>
                </div>

                @if ($job->responsibilities)
                    <div class="space-y-3">
                        <h2 class="text-xl font-semibold text-slate-950">Responsabilités</h2>
                        <p class="text-sm leading-7">{{ $job->responsibilities }}</p>
                    </div>
                @endif

                @if ($job->requirements)
                    <div class="space-y-3">
                        <h2 class="text-xl font-semibold text-slate-950">Exigences</h2>
                        <p class="text-sm leading-7">{{ $job->requirements }}</p>
                    </div>
                @endif

                @if ($job->skills)
                    <div>
                        <h2 class="text-lg font-semibold text-slate-950">Compétences clés</h2>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($job->skills as $skill)
                                <span class="info-chip">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            @if (auth()->user()->role === 'recruiter' && auth()->id() === $job->recruiter_id)
                <div class="mt-8 flex flex-wrap gap-3">
                    <a class="btn-secondary" href="{{ route('recruiter.jobs.edit', $job) }}">Modifier</a>
                    <form method="POST" action="{{ route('recruiter.jobs.destroy', $job) }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn-primary bg-rose-600 hover:bg-rose-500">Supprimer</button>
                    </form>
                </div>
            @endif
        </article>

        <aside class="space-y-6">
            @if (auth()->user()->role === 'candidate')
                <div class="card p-6">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-950">Postuler</h2>
                            <p class="mt-1 text-sm text-slate-500">Sélectionnez un CV et personnalisez votre candidature.</p>
                        </div>
                        @if ($existingApplication)
                            <span class="badge">Postulé</span>
                        @endif
                    </div>

                    @if ($existingApplication)
                        <div class="mt-5 rounded-[1.5rem] border border-emerald-200 bg-emerald-50 px-4 py-4 text-sm text-emerald-800">
                            Vous avez déjà soumis une candidature pour cette offre. Consultez votre page Candidatures pour suivre le statut.
                        </div>
                    @else
                        <form method="POST" action="{{ route('jobs.apply', $job) }}" class="mt-5 space-y-5">
                            @csrf
                            <div>
                                <label class="label">CV</label>
                                <select class="input-field" name="resume_id">
                                    <option value="">Utiliser le dernier CV</option>
                                    @foreach ($resumes as $resume)
                                        <option value="{{ $resume->id }}">{{ $resume->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label">Lettre de motivation</label>
                                <textarea class="input-field min-h-36" name="cover_letter"></textarea>
                            </div>
                            <div>
                                <label class="label">Notes candidat</label>
                                <textarea class="input-field min-h-28" name="candidate_notes"></textarea>
                            </div>
                            <button class="btn-primary w-full">Envoyer la candidature</button>
                        </form>
                    @endif
                </div>
            @endif

            <div class="card p-6">
                <h2 class="text-lg font-semibold text-slate-950">Informations entreprise</h2>
                <div class="mt-4 space-y-3 text-sm text-slate-600">
                    <p><span class="font-semibold text-slate-900">Nom:</span> {{ $job->companyProfile?->company_name ?? $job->recruiter->company_name ?? $job->recruiter->name }}</p>
                    <p><span class="font-semibold text-slate-900">Industrie:</span> {{ $job->companyProfile?->industry ?? $job->recruiter->company_industry ?? 'N/A' }}</p>
                    <p><span class="font-semibold text-slate-900">Salaire:</span> {{ $job->salary_min }} - {{ $job->salary_max }} {{ $job->currency }}</p>
                    <p><span class="font-semibold text-slate-900">Expiration:</span> {{ $job->expires_at?->format('d/m/Y') ?? 'Non précisée' }}</p>
                </div>
            </div>
        </aside>
    </div>
@endsection
