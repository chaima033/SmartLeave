@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <span class="badge">Profil</span>
        <h1 class="section-title mt-4">Gestion du profil</h1>
        <p class="section-subtitle">Mettez à jour vos informations personnelles et professionnelles en un seul endroit.</p>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" class="card-lg space-y-8 p-8">
        @csrf
        @method('PUT')

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="field-group">
                <label class="label">Nom</label>
                <input class="input-field" name="name" value="{{ old('name', auth()->user()->name) }}" required>
            </div>
            <div class="field-group">
                <label class="label">Téléphone</label>
                <input class="input-field" name="phone" value="{{ old('phone', auth()->user()->phone) }}">
            </div>
            <div class="field-group">
                <label class="label">Localisation</label>
                <input class="input-field" name="location" value="{{ old('location', auth()->user()->location) }}">
            </div>
            <div class="field-group">
                <label class="label">Titre</label>
                <input class="input-field" name="headline" value="{{ old('headline', auth()->user()->headline) }}">
            </div>
        </div>

        <div class="field-group">
            <label class="label">Bio</label>
            <textarea class="input-field min-h-32" name="bio">{{ old('bio', auth()->user()->bio) }}</textarea>
        </div>

        @if (auth()->user()->role === 'recruiter')
            <div class="field-group">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-base font-semibold text-slate-950">Profil entreprise</p>
                        <p class="text-sm text-slate-500">Présentez votre entreprise avec clarté et confiance.</p>
                    </div>
                </div>
                <div class="mt-6 grid gap-6 lg:grid-cols-2">
                    <div>
                        <label class="label">Entreprise</label>
                        <input class="input-field" name="company_name" value="{{ old('company_name', auth()->user()->companyProfile?->company_name ?? auth()->user()->company_name) }}" required>
                    </div>
                    <div>
                        <label class="label">Secteur</label>
                        <input class="input-field" name="company_industry" value="{{ old('company_industry', auth()->user()->companyProfile?->industry ?? auth()->user()->company_industry) }}">
                    </div>
                    <div>
                        <label class="label">Site web</label>
                        <input class="input-field" name="company_website" value="{{ old('company_website', auth()->user()->companyProfile?->website ?? auth()->user()->company_website) }}">
                    </div>
                    <div>
                        <label class="label">Taille</label>
                        <input class="input-field" name="company_size" value="{{ old('company_size', auth()->user()->companyProfile?->size ?? auth()->user()->company_size) }}">
                    </div>
                </div>
                <div class="mt-6">
                    <label class="label">Description entreprise</label>
                    <textarea class="input-field min-h-32" name="company_description">{{ old('company_description', auth()->user()->companyProfile?->description ?? auth()->user()->company_description) }}</textarea>
                </div>
            </div>
        @else
            <div class="field-group">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-base font-semibold text-slate-950">Profil candidat</p>
                        <p class="text-sm text-slate-500">Affinez votre candidature en renseignant vos objectifs et compétences.</p>
                    </div>
                </div>
                <div class="mt-6 grid gap-6 lg:grid-cols-2">
                    <div>
                        <label class="label">Rôle souhaité</label>
                        <input class="input-field" name="desired_role" value="{{ old('desired_role', auth()->user()->candidateProfile?->desired_role) }}">
                    </div>
                    <div>
                        <label class="label">Années d'expérience</label>
                        <input class="input-field" type="number" name="experience_years" value="{{ old('experience_years', auth()->user()->candidateProfile?->experience_years) }}">
                    </div>
                    <div>
                        <label class="label">Compétences</label>
                        <input class="input-field" name="skills" value="{{ old('skills', isset(auth()->user()->candidateProfile?->skills) ? implode(', ', auth()->user()->candidateProfile->skills ?? []) : '') }}">
                    </div>
                    <div>
                        <label class="label">Prétention salaire</label>
                        <input class="input-field" name="salary_expectation" value="{{ old('salary_expectation', auth()->user()->candidateProfile?->salary_expectation) }}">
                    </div>
                    <div class="lg:col-span-2">
                        <label class="label">Portfolio</label>
                        <input class="input-field" name="portfolio_url" value="{{ old('portfolio_url', auth()->user()->candidateProfile?->portfolio_url) }}">
                    </div>
                </div>
            </div>
        @endif

        <button class="btn-primary">Enregistrer</button>
    </form>
@endsection
