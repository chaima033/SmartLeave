@extends('layouts.app')

@section('content')
    <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <div class="badge">CV</div>
            <h1 class="section-title mt-4">Mes CV</h1>
            <p class="mt-2 text-slate-600">Creer, modifier et definir votre CV principal.</p>
        </div>
        <a class="btn-primary" href="{{ route('resumes.create') }}">Nouveau CV</a>
    </div>

    <div class="grid gap-5">
        @forelse ($resumes as $resume)
            <article class="surface p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-950">{{ $resume->title }}</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $resume->is_primary ? 'CV principal' : 'CV secondaire' }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a class="btn-secondary px-4 py-2" href="{{ route('resumes.edit', $resume) }}">Modifier</a>
                        <form method="POST" action="{{ route('resumes.destroy', $resume) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn-secondary px-4 py-2 text-rose-700">Supprimer</button>
                        </form>
                    </div>
                </div>

                <div class="mt-5 grid gap-4 lg:grid-cols-2 text-sm text-slate-600">
                    <div><span class="font-semibold text-slate-900">Experience:</span> {{ $resume->experience ?? 'N/A' }}</div>
                    <div><span class="font-semibold text-slate-900">Education:</span> {{ $resume->education ?? 'N/A' }}</div>
                    <div><span class="font-semibold text-slate-900">Langues:</span> {{ $resume->languages ?? 'N/A' }}</div>
                    <div><span class="font-semibold text-slate-900">Competences:</span> {{ implode(', ', $resume->skills ?? []) }}</div>
                </div>
            </article>
        @empty
            <div class="surface p-8 text-center text-slate-500">Aucun CV cree pour le moment.</div>
        @endforelse
    </div>
@endsection
