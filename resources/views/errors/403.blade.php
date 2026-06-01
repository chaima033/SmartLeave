@extends('layouts.guest')

@section('content')
    <div class="text-center">
        <div class="badge mx-auto">403</div>
        <h1 class="section-title mt-4">Acces refuse</h1>
        <p class="mt-2 text-slate-600">Vous n'avez pas les droits pour acceder a cette ressource.</p>
        <a class="btn-primary mt-6" href="{{ route('dashboard') }}">Retour au tableau de bord</a>
    </div>
@endsection
