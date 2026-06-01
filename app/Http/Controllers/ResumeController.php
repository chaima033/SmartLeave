<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resume\StoreResumeRequest;
use App\Http\Requests\Resume\UpdateResumeRequest;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ResumeController extends Controller
{
    public function index(Request $request): View
    {
        return view('resumes.index', [
            'resumes' => Resume::query()->where('user_id', $request->user()->id)->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('resumes.form', [
            'resume' => new Resume(),
        ]);
    }

    public function store(StoreResumeRequest $request): RedirectResponse
    {
        $data = $request->validated();

        Resume::create($data + [
            'user_id' => $request->user()->id,
            'skills' => $this->parseSkills($data['skills'] ?? null),
            'is_primary' => $request->boolean('is_primary'),
        ]);

        return redirect()->route('resumes.index')->with('status', 'CV cree.');
    }

    public function edit(Request $request, Resume $resume): View
    {
        abort_unless($resume->user_id === $request->user()->id, 403);

        return view('resumes.form', [
            'resume' => $resume,
        ]);
    }

    public function update(UpdateResumeRequest $request, Resume $resume): RedirectResponse
    {
        abort_unless($resume->user_id === $request->user()->id, 403);

        $data = $request->validated();

        $resume->update($data + [
            'skills' => $this->parseSkills($data['skills'] ?? null),
            'is_primary' => $request->boolean('is_primary'),
        ]);

        return redirect()->route('resumes.index')->with('status', 'CV mis a jour.');
    }

    public function destroy(Request $request, Resume $resume): RedirectResponse
    {
        abort_unless($resume->user_id === $request->user()->id, 403);

        $resume->delete();

        return back()->with('status', 'CV supprime.');
    }

    public function recruiterIndex(): View
    {
        return view('recruiter.resumes.index', [
            'candidates' => User::query()
                ->where('role', 'candidate')
                ->with(['candidateProfile', 'resumes' => fn($query) => $query->latest()])
                ->latest()
                ->paginate(12),
        ]);
    }

    public function recruiterShow(User $user): View
    {
        abort_unless($user->role === 'candidate', 404);

        return view('recruiter.resumes.show', [
            'candidate' => $user->load(['candidateProfile', 'resumes' => fn($query) => $query->latest()]),
        ]);
    }

    private function parseSkills(?string $skills): array
    {
        return array_values(array_filter(array_map('trim', explode(',', $skills ?? ''))));
    }
}
