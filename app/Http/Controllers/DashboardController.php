<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\JobOffer;
use App\Models\Resume;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->role === 'recruiter')
        {
            $offers = JobOffer::query()->where('recruiter_id', $user->id)->latest()->take(5)->get();
            $applications = Application::query()
                ->whereHas('jobOffer', fn($query) => $query->where('recruiter_id', $user->id))
                ->latest()
                ->take(5)
                ->get();

            return view('dashboard', [
                'mode' => 'recruiter',
                'offersCount' => JobOffer::query()->where('recruiter_id', $user->id)->count(),
                'applicationsCount' => Application::query()->whereHas('jobOffer', fn($query) => $query->where('recruiter_id', $user->id))->count(),
                'offers' => $offers,
                'applications' => $applications,
            ]);
        }

        return view('dashboard', [
            'mode' => 'candidate',
            'resumesCount' => Resume::query()->where('user_id', $user->id)->count(),
            'applicationsCount' => Application::query()->where('candidate_id', $user->id)->count(),
            'jobsCount' => JobOffer::query()->where('status', 'published')->count(),
            'applications' => Application::query()->where('candidate_id', $user->id)->latest()->take(5)->get(),
            'jobs' => JobOffer::query()->where('status', 'published')->latest()->take(5)->get(),
        ]);
    }
}
