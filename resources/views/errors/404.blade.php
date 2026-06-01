@extends('layouts.guest')

@section('content')
    <div class="text-center">
        <div class="badge mx-auto">404</div>
        <h1 class="section-title mt-4">Page introuvable</h1>
        <p class="mt-2 text-slate-600">La page demandee n'existe pas ou a ete deplacee.</p>
        <a class="btn-primary mt-6" href="{{ route('dashboard') }}">Retour au tableau de bord</a>
    </div>
@endsection
