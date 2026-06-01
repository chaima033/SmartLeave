<?php

namespace App\Http\Controllers;

use App\Http\Requests\Application\StoreApplicationRequest;
use App\Http\Requests\Application\UpdateApplicationStatusRequest;
use App\Models\Application;
use App\Models\JobOffer;
use App\Models\Resume;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index(Request $request): View
    {
        return view('applications.index', [
            'applications' => Application::query()
                ->with(['jobOffer.companyProfile', 'resume'])
                ->where('candidate_id', $request->user()->id)
                ->latest()
                ->paginate(10),
        ]);
    }

    public function store(StoreApplicationRequest $request, JobOffer $jobOffer): RedirectResponse
    {
        $data = $request->validated();

        $existingApplication = Application::query()
            ->where('job_offer_id', $jobOffer->id)
            ->where('candidate_id', $request->user()->id)
            ->first();

        if ($existingApplication)
        {
            return back()->with('status', 'Vous avez déjà postulé à cette offre.');
        }

        $resume = ! empty($data['resume_id'])
            ? Resume::query()->where('user_id', $request->user()->id)->find($data['resume_id'])
            : Resume::query()->where('user_id', $request->user()->id)->latest()->first();

        Application::create([
            'job_offer_id' => $jobOffer->id,
            'candidate_id' => $request->user()->id,
            'resume_id' => $resume?->id,
            'status' => 'submitted',
            'cover_letter' => $data['cover_letter'] ?? null,
            'resume_snapshot' => $resume ? $resume->toArray() : null,
            'candidate_notes' => $data['candidate_notes'] ?? null,
        ]);

        return back()->with('status', 'Candidature envoyée.');
    }

    public function recruiterIndex(Request $request): View
    {
        return view('recruiter.applications.index', [
            'applications' => Application::query()
                ->with(['jobOffer', 'candidate', 'resume'])
                ->whereHas('jobOffer', fn($query) => $query->where('recruiter_id', $request->user()->id))
                ->latest()
                ->paginate(10),
        ]);
    }

    public function update(UpdateApplicationStatusRequest $request, Application $application): RedirectResponse
    {
        $data = $request->validated();

        $application->update([
            'status' => $data['status'],
            'recruiter_feedback' => $data['recruiter_feedback'] ?? null,
            'selected_at' => $data['status'] === 'hired' ? now() : $application->selected_at,
        ]);

        return back()->with('status', 'Statut mis à jour.');
    }
}
