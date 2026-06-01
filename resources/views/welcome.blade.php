<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'TalentFlow AI') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|space-grotesk:400,500,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-shell font-sans text-slate-900">
    <main class="mx-auto flex min-h-screen max-w-7xl items-center px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid w-full gap-6 lg:grid-cols-[1.05fr_0.95fr]">
            <section class="surface overflow-hidden p-8 lg:p-12">
                <div class="badge">Plateforme recrutement</div>
                <h1 class="page-title mt-5 max-w-3xl">Candidats, recruteurs et assistant IA dans un seul produit.</h1>
                <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-600">
                    Publiez des offres, gerez les candidatures, construisez des CV et obtenez des conseils IA branchés sur Gemini.
                </p>

                <div class="mt-8 grid gap-4 sm:grid-cols-3">
                    <div class="stat-card">
                        <div class="text-3xl font-bold text-slate-950">2</div>
                        <p class="mt-2 text-sm text-slate-600">Roles pris en charge</p>
                    </div>
                    <div class="stat-card">
                        <div class="text-3xl font-bold text-slate-950">AI</div>
                        <p class="mt-2 text-sm text-slate-600">Conseil emploi et recrutement</p>
                    </div>
                    <div class="stat-card">
                        <div class="text-3xl font-bold text-slate-950">DB</div>
                        <p class="mt-2 text-sm text-slate-600">Auth, profils, offres, candidatures</p>
                    </div>
                </div>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="btn-primary">Creer un compte</a>
                    <a href="{{ route('login') }}" class="btn-secondary">Se connecter</a>
                </div>
            </section>

            <aside class="surface p-8 lg:p-12">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-slate-950">Ce que couvre l application</h2>
                    <span class="badge">Demo</span>
                </div>

                <div class="space-y-4 text-sm leading-6 text-slate-600">
                    <div class="rounded-3xl border border-slate-200 bg-white p-5">
                        <p class="font-semibold text-slate-950">Candidat</p>
                        <p class="mt-2">Inscription, connexion, profil, CV, recherche d emploi, candidatures, suivi et assistant IA.</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-white p-5">
                        <p class="font-semibold text-slate-950">Recruteur</p>
                        <p class="mt-2">Profil entreprise, publication d offres, gestion des candidatures, consultation des CV et selection.</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-slate-950 p-5 text-white">
                        <p class="font-semibold">Gemini</p>
                        <p class="mt-2 text-slate-300">Assistant IA alimente par l API Gemini via le endpoint configure dans l environnement.</p>
                    </div>
                </div>
            </aside>
        </div>
    </main>
</body>
</html>
