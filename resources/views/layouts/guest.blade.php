<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SmartLeave') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|space-grotesk:400,500,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-white">
    <div class="relative">
        <div class="hero-glow"></div>
        <div class="page-layout py-10">
            <div class="mx-auto grid gap-8 lg:grid-cols-[1.05fr_0.95fr]">
                <section class="hero-panel">
                    <span class="badge bg-white/10 border-white/15 text-white">Plateforme SaaS</span>
                    <h1 class="mt-6 text-4xl font-semibold text-white sm:text-5xl">Recrutement moderne pour candidats et recruteurs.</h1>
                    <p class="mt-4 max-w-2xl text-slate-300">Une interface épurée, des flux intuitifs et une expérience conçue pour accélérer les embauches et les candidatures.</p>

                    <div class="mt-10 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-[1.75rem] bg-white/10 p-6">
                            <p class="text-sm uppercase tracking-[0.28em] text-slate-300">Design</p>
                            <p class="mt-3 text-lg font-semibold text-white">Premium, professionnel et rassurant</p>
                        </div>
                        <div class="rounded-[1.75rem] bg-white/10 p-6">
                            <p class="text-sm uppercase tracking-[0.28em] text-slate-300">Flux</p>
                            <p class="mt-3 text-lg font-semibold text-white">Navigation rapide pour chaque rôle</p>
                        </div>
                    </div>
                </section>

                <section class="card-lg p-8 lg:p-10">
                    <div class="mb-8 space-y-4">
                        <span class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-300">Connectez-vous</span>
                        <h2 class="section-title text-slate-950">Bienvenue dans SmartLeave</h2>
                        <p class="section-subtitle text-slate-600">Accédez à votre espace avec un tableau de bord clair et des actions rapides.</p>
                    </div>

                    @yield('content')
                </section>
            </div>
        </div>
    </div>
</body>
</html>
