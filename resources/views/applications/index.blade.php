@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <span class="badge">Candidat</span>
        <h1 class="section-title mt-4">Mes candidatures</h1>
        <p class="section-subtitle">Suivez l'avancement de vos candidatures et gardez le contrôle de votre parcours.</p>
    </div>

    <div class="space-y-6">
        @forelse ($applications as $application)
            <article class="card p-6 transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_24px_60px_rgba(15,23,42,0.08)]">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-950">{{ $application->jobOffer->title }}</h2>
                        <p class="mt-2 text-sm text-slate-500">{{ $application->jobOffer->companyProfile?->company_name ?? $application->jobOffer->recruiter->name }}</p>
                    </div>
                    <span class="status-pill status-pill--{{ $application->status }}">{{ ucfirst($application->status) }}</span>
                </div>
                <div class="mt-5 grid gap-4 sm:grid-cols-2 text-sm leading-6 text-slate-600">
                    <div><span class="font-semibold text-slate-900">Résumé :</span> {{ $application->resume?->title ?? 'Aucun' }}</div>
                    <div><span class="font-semibold text-slate-900">Envoyée le :</span> {{ $application->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </article>
        @empty
            <div class="card p-10 text-center text-slate-500">Aucune candidature pour le moment.</div>
        @endforelse
    </div>

    <div class="mt-8">{{ $applications->links() }}</div>
@endsection
