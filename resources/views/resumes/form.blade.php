@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <div class="badge">CV</div>
        <h1 class="section-title mt-4">{{ $resume->exists ? 'Modifier le CV' : 'Creer un CV' }}</h1>
    </div>

    <form method="POST" action="{{ $resume->exists ? route('resumes.update', $resume) : route('resumes.store') }}" class="surface space-y-6 p-6 lg:p-8">
        @csrf
        @if ($resume->exists)
            @method('PUT')
        @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="label">Titre</label>
                <input class="input-field" name="title" value="{{ old('title', $resume->title) }}" required>
            </div>
            <div>
                <label class="label">Experience</label>
                <textarea class="input-field min-h-28" name="experience">{{ old('experience', $resume->experience) }}</textarea>
            </div>
            <div>
                <label class="label">Education</label>
                <textarea class="input-field min-h-28" name="education">{{ old('education', $resume->education) }}</textarea>
            </div>
            <div>
                <label class="label">Competences comma separated</label>
                <input class="input-field" name="skills" value="{{ old('skills', isset($resume->skills) ? implode(', ', $resume->skills ?? []) : '') }}">
            </div>
            <div>
                <label class="label">Langues</label>
                <input class="input-field" name="languages" value="{{ old('languages', $resume->languages) }}">
            </div>
            <div>
                <label class="label">Certifications</label>
                <textarea class="input-field min-h-28" name="certifications">{{ old('certifications', $resume->certifications) }}</textarea>
            </div>
            <div>
                <label class="label">Projets</label>
                <textarea class="input-field min-h-28" name="projects">{{ old('projects', $resume->projects) }}</textarea>
            </div>
        </div>

        <div>
            <label class="label">Summary</label>
            <textarea class="input-field min-h-32" name="summary">{{ old('summary', $resume->summary) }}</textarea>
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="is_primary" value="1" @checked(old('is_primary', $resume->is_primary)) class="rounded border-slate-300">
            Definir comme CV principal
        </label>

        <button class="btn-primary">{{ $resume->exists ? 'Mettre a jour' : 'Creer le CV' }}</button>
    </form>
@endsection
