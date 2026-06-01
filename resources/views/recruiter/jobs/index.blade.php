@extends('layouts.app')

@section('content')
    <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <span class="badge">Recruteur</span>
            <h1 class="section-title mt-4">Mes offres</h1>
            <p class="section-subtitle">Publiez, modifiez et suivez vos offres avec une vue structurée.</p>
        </div>
        <a class="btn-primary" href="{{ route('recruiter.jobs.create') }}">Nouvelle offre</a>
    </div>

    <div class="table-panel">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.2em] text-slate-500">
                <tr>
                    <th class="px-6 py-4">Titre</th>
                    <th class="px-6 py-4">Lieu</th>
                    <th class="px-6 py-4">Statut</th>
                    <th class="px-6 py-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($jobs as $job)
                    <tr class="table-row">
                        <td class="px-6 py-5 font-semibold text-slate-950">{{ $job->title }}</td>
                        <td class="px-6 py-5 text-slate-600">{{ $job->location ?? 'Remote' }}</td>
                        <td class="px-6 py-5"><span class="status-pill status-pill--{{ $job->status }}">{{ ucfirst($job->status) }}</span></td>
                        <td class="px-6 py-5">
                            <div class="flex flex-wrap gap-3">
                                <a class="btn-ghost" href="{{ route('recruiter.jobs.edit', $job) }}">Modifier</a>
                                <form method="POST" action="{{ route('recruiter.jobs.destroy', $job) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-ghost text-rose-600">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-slate-500">Aucune offre pour le moment.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-8">{{ $jobs->links() }}</div>
@endsection
