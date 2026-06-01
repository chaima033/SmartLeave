@extends('layouts.guest')

@section('content')
    <div class="space-y-8">
        <div class="space-y-3">
            <span class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-500">Connexion</span>
            <h2 class="section-title text-slate-950">Bienvenue dans votre espace recrutement</h2>
            <p class="section-subtitle text-slate-600">Connectez-vous pour accéder à vos candidatures, vos offres et votre assistant intelligent.</p>
        </div>

        <form method="POST" action="{{ route('login.store') }}" class="space-y-6">
            @csrf

            <div class="field-group">
                <label class="label">Email</label>
                <input class="input-field" type="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="field-group">
                <label class="label">Mot de passe</label>
                <input class="input-field" type="password" name="password" required>
            </div>

            <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                Se souvenir de moi
            </label>

            <button class="btn-primary w-full justify-center">Se connecter</button>
        </form>

        <p class="text-center text-sm text-slate-500">Pas encore de compte ? <a class="font-semibold text-slate-950 underline" href="{{ route('register') }}">Créer un compte</a></p>
    </div>
@endsection
