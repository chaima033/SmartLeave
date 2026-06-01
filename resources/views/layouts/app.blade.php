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
<body class="page-shell">
    <div class="page-layout">
        <header class="site-header">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-2">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3 text-xl font-semibold tracking-tight text-slate-950">
                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-950 text-white">S</span>
                        <span>{{ config('app.name', 'SmartLeave') }}</span>
                    </a>
                    <p class="text-sm text-slate-500">Une interface premium pour candidats et recruteurs.</p>
                </div>
                <nav class="flex flex-wrap items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="nav-pill">Dashboard</a>
                        <a href="{{ route('jobs.index') }}" class="nav-pill">Offres</a>
                        @if (auth()->user()->role === 'candidate')
                            <a href="{{ route('resumes.index') }}" class="nav-pill">Mes CV</a>
                        @endif
                        @if (auth()->user()->role === 'recruiter')
                            <a href="{{ route('recruiter.jobs.index') }}" class="nav-pill">Mes offres</a>
                            <a href="{{ route('recruiter.applications.index') }}" class="nav-pill">Recrutement</a>
                        @endif
                        <a href="{{ route('assistant.index') }}" class="nav-pill">Assistant IA</a>
                        <a href="{{ route('profile.edit') }}" class="nav-pill">Profil</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="btn-primary">Deconnexion</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="nav-pill">Connexion</a>
                        <a href="{{ route('register') }}" class="btn-primary">Inscription</a>
                    @endauth
                </nav>
            </div>

            @auth
                <div class="mt-6 flex flex-wrap items-center justify-between gap-4 rounded-[1.5rem] border border-slate-200/70 bg-slate-50/90 p-4 text-sm text-slate-600 shadow-sm">
                    <span class="font-medium text-slate-900">Connecte en tant que {{ auth()->user()->role === 'recruiter' ? 'Recruteur' : 'Candidat' }} :</span>
                    <span>{{ auth()->user()->name }}</span>
                    <a href="{{ route('dashboard') }}" class="btn-ghost">Mon tableau de bord</a>
                </div>
            @endauth
        </header>

        @if (session('status'))
            <div class="mb-6 rounded-[1.5rem] border border-emerald-200 bg-emerald-50 px-4 py-4 text-sm text-emerald-800 shadow-sm">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-[1.5rem] border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-700 shadow-sm">
                <ul class="space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <main class="space-y-8">
            @yield('content')
        </main>
    </div>
</body>
</html>
