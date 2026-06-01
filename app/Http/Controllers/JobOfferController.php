<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobOffer\StoreJobOfferRequest;
use App\Http\Requests\JobOffer\UpdateJobOfferRequest;
use App\Models\JobOffer;
use App\Models\Resume;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class JobOfferController extends Controller
{
    public function index(Request $request): View
    {
        $query = JobOffer::query()->with(['recruiter', 'companyProfile'])->where('status', 'published');

        if ($search = $request->string('search')->trim()->toString())
        {
            $query->where(function ($builder) use ($search): void
            {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return view('jobs.index', [
            'jobs' => $query->latest()->paginate(9)->withQueryString(),
        ]);
    }

    public function show(Request $request, JobOffer $jobOffer): View
    {
        $user = $request->user();

        return view('jobs.show', [
            'job' => $jobOffer->load(['recruiter', 'companyProfile', 'applications.candidate']),
            'resumes' => $user ? Resume::query()->where('user_id', $user->id)->latest()->get() : collect(),
            'existingApplication' => $user && $user->role === 'candidate'
                ? $jobOffer->applications->firstWhere('candidate_id', $user->id)
                : null,
        ]);
    }

    public function manage(Request $request): View
    {
        $jobs = JobOffer::query()->where('recruiter_id', $request->user()->id)->latest()->paginate(10);

        return view('recruiter.jobs.index', [
            'jobs' => $jobs,
        ]);
    }

    public function create(): View
    {
        return view('recruiter.jobs.form', [
            'job' => new JobOffer(),
        ]);
    }

    public function store(StoreJobOfferRequest $request): RedirectResponse
    {
        $data = $request->validated();

        JobOffer::create([
            'recruiter_id' => $request->user()->id,
            'company_profile_id' => $request->user()->companyProfile?->id,
            'title' => $data['title'],
            'slug' => Str::slug($data['title']) . '-' . Str::random(6),
            'location' => $data['location'] ?? null,
            'contract_type' => $data['contract_type'] ?? null,
            'work_mode' => $data['work_mode'] ?? null,
            'salary_min' => $data['salary_min'] ?? null,
            'salary_max' => $data['salary_max'] ?? null,
            'currency' => $data['currency'] ?? 'EUR',
            'description' => $data['description'],
            'responsibilities' => $data['responsibilities'] ?? null,
            'requirements' => $data['requirements'] ?? null,
            'skills' => array_values(array_filter(array_map('trim', explode(',', $data['skills'] ?? '')))),
            'status' => $data['status'],
            'expires_at' => $data['expires_at'] ?? null,
        ]);

        return redirect()->route('recruiter.jobs.index')->with('status', 'Offre publiée.');
    }

    public function edit(JobOffer $jobOffer): View
    {
        return view('recruiter.jobs.form', [
            'job' => $jobOffer,
        ]);
    }

    public function update(UpdateJobOfferRequest $request, JobOffer $jobOffer): RedirectResponse
    {
        $data = $request->validated();

        $jobOffer->update([
            'title' => $data['title'],
            'location' => $data['location'] ?? null,
            'contract_type' => $data['contract_type'] ?? null,
            'work_mode' => $data['work_mode'] ?? null,
            'salary_min' => $data['salary_min'] ?? null,
            'salary_max' => $data['salary_max'] ?? null,
            'currency' => $data['currency'] ?? 'EUR',
            'description' => $data['description'],
            'responsibilities' => $data['responsibilities'] ?? null,
            'requirements' => $data['requirements'] ?? null,
            'skills' => array_values(array_filter(array_map('trim', explode(',', $data['skills'] ?? '')))),
            'status' => $data['status'],
            'expires_at' => $data['expires_at'] ?? null,
        ]);

        return redirect()->route('recruiter.jobs.index')->with('status', 'Offre mise à jour.');
    }

    public function destroy(JobOffer $jobOffer): RedirectResponse
    {
        $jobOffer->delete();

        return back()->with('status', 'Offre supprimée.');
    }
}
