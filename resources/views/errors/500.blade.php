@extends('layouts.guest')

@section('content')
    <div class="text-center">
        <div class="badge mx-auto">500</div>
        <h1 class="section-title mt-4">Erreur serveur</h1>
        <p class="mt-2 text-slate-600">Une erreur interne est survenue. Reessayez plus tard.</p>
        <a class="btn-primary mt-6" href="{{ route('dashboard') }}">Retour au tableau de bord</a>
    </div>
@endsection
