@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <div class="badge">Offre</div>
        <h1 class="section-title mt-4">{{ $job->exists ? 'Modifier une offre' : 'Creer une offre' }}</h1>
    </div>

    <form method="POST" action="{{ $job->exists ? route('recruiter.jobs.update', $job) : route('recruiter.jobs.store') }}" class="surface space-y-6 p-6 lg:p-8">
        @csrf
        @if ($job->exists)
            @method('PUT')
        @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="label">Titre</label>
                <input class="input-field" name="title" value="{{ old('title', $job->title) }}" required>
            </div>
            <div>
                <label class="label">Localisation</label>
                <input class="input-field" name="location" value="{{ old('location', $job->location) }}">
            </div>
            <div>
                <label class="label">Type de contrat</label>
                <input class="input-field" name="contract_type" value="{{ old('contract_type', $job->contract_type) }}" placeholder="CDI, CDD, Stage...">
            </div>
            <div>
                <label class="label">Mode de travail</label>
                <input class="input-field" name="work_mode" value="{{ old('work_mode', $job->work_mode) }}" placeholder="Remote, hybride, sur site">
            </div>
            <div>
                <label class="label">Devise</label>
                <input class="input-field" name="currency" value="{{ old('currency', $job->currency ?? 'EUR') }}">
            </div>
            <div>
                <label class="label">Salaire min</label>
                <input class="input-field" name="salary_min" value="{{ old('salary_min', $job->salary_min) }}">
            </div>
            <div>
                <label class="label">Salaire max</label>
                <input class="input-field" name="salary_max" value="{{ old('salary_max', $job->salary_max) }}">
            </div>
            <div>
                <label class="label">Statut</label>
                <select class="input-field" name="status">
                    @foreach (['draft', 'published', 'closed'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $job->status ?? 'published') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="label">Date d expiration</label>
                <input class="input-field" type="date" name="expires_at" value="{{ old('expires_at', optional($job->expires_at)->format('Y-m-d')) }}">
            </div>
        </div>

        <div>
            <label class="label">Description</label>
            <textarea class="input-field min-h-40" name="description" required>{{ old('description', $job->description) }}</textarea>
        </div>

        <div>
            <label class="label">Responsabilites</label>
            <textarea class="input-field min-h-32" name="responsibilities">{{ old('responsibilities', $job->responsibilities) }}</textarea>
        </div>

        <div>
            <label class="label">Exigences</label>
            <textarea class="input-field min-h-32" name="requirements">{{ old('requirements', $job->requirements) }}</textarea>
        </div>

        <div>
            <label class="label">Competences comma separated</label>
            <input class="input-field" name="skills" value="{{ old('skills', isset($job->skills) ? implode(', ', $job->skills ?? []) : '') }}">
        </div>

        <button class="btn-primary">{{ $job->exists ? 'Mettre a jour' : 'Publier' }}</button>
    </form>
@endsection
