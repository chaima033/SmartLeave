@extends('layouts.guest')

@section('content')
    <div class="space-y-8">
        <div class="space-y-3">
            <span class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-500">Inscription</span>
            <h2 class="section-title text-slate-950">Créez votre espace professionnel</h2>
            <p class="section-subtitle text-slate-600">Partagez votre profil, publiez des offres et suivez vos candidats en toute simplicité.</p>
        </div>

        <form method="POST" action="{{ route('register.store') }}" class="space-y-8">
            @csrf

            <div class="grid gap-6 sm:grid-cols-2">
                <div class="field-group">
                    <label class="label">Nom complet</label>
                    <input class="input-field" type="text" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="field-group">
                    <label class="label">Email</label>
                    <input class="input-field" type="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="field-group">
                    <label class="label">Mot de passe</label>
                    <input class="input-field" type="password" name="password" required>
                </div>
                <div class="field-group">
                    <label class="label">Confirmation</label>
                    <input class="input-field" type="password" name="password_confirmation" required>
                </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div class="field-group">
                    <label class="label">Rôle</label>
                    <select class="input-field" name="role" required>
                        <option value="candidate">Candidat</option>
                        <option value="recruiter">Recruteur</option>
                    </select>
                </div>
                <div class="field-group">
                    <label class="label">Téléphone</label>
                    <input class="input-field" type="text" name="phone" value="{{ old('phone') }}">
                </div>
                <div class="field-group">
                    <label class="label">Localisation</label>
                    <input class="input-field" type="text" name="location" value="{{ old('location') }}">
                </div>
                <div class="field-group">
                    <label class="label">Titre / headline</label>
                    <input class="input-field" type="text" name="headline" value="{{ old('headline') }}" placeholder="Ex: Développeur Laravel ou Responsable RH">
                </div>
            </div>

            <div class="field-group">
                <label class="label">Bio</label>
                <textarea class="input-field min-h-28" name="bio">{{ old('bio') }}</textarea>
            </div>

            <div class="field-group">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Profil entreprise</p>
                        <p class="text-sm text-slate-500">Remplissez ces informations si vous êtes recruteur.</p>
                    </div>
                </div>

                <div class="mt-6 grid gap-6 sm:grid-cols-2">
                    <div>
                        <label class="label">Nom entreprise</label>
                        <input class="input-field" type="text" name="company_name" value="{{ old('company_name') }}">
                    </div>
                    <div>
                        <label class="label">Secteur</label>
                        <input class="input-field" type="text" name="company_industry" value="{{ old('company_industry') }}">
                    </div>
                    <div>
                        <label class="label">Site web</label>
                        <input class="input-field" type="url" name="company_website" value="{{ old('company_website') }}">
                    </div>
                    <div>
                        <label class="label">Taille</label>
                        <input class="input-field" type="text" name="company_size" value="{{ old('company_size') }}" placeholder="10-50, 50-200...">
                    </div>
                </div>

                <div class="mt-6">
                    <label class="label">Description entreprise</label>
                    <textarea class="input-field min-h-28" name="company_description">{{ old('company_description') }}</textarea>
                </div>
            </div>

            <button class="btn-primary w-full">Créer le compte</button>
        </form>

        <p class="text-center text-sm text-slate-500">Déjà inscrit ? <a class="font-semibold text-slate-950 underline" href="{{ route('login') }}">Se connecter</a></p>
    </div>
@endsection
