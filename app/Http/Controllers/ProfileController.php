<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\CandidateProfile;
use App\Models\CompanyProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();

        return view('profile.edit', [
            'candidateProfile' => $user->candidateProfile,
            'companyProfile' => $user->companyProfile,
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validated();

        if ($user->role === 'recruiter')
        {
            $user->update([
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'location' => $data['location'] ?? null,
                'company_name' => $data['company_name'] ?? null,
                'company_industry' => $data['company_industry'] ?? null,
                'company_website' => $data['company_website'] ?? null,
                'company_size' => $data['company_size'] ?? null,
                'company_description' => $data['company_description'] ?? null,
                'bio' => $data['bio'] ?? null,
            ]);

            CompanyProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'company_name' => $data['company_name'],
                    'industry' => $data['company_industry'] ?? null,
                    'website' => $data['company_website'] ?? null,
                    'size' => $data['company_size'] ?? null,
                    'location' => $data['location'] ?? null,
                    'description' => $data['company_description'] ?? null,
                ]
            );
        }
        else
        {
            $user->update([
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'headline' => $data['headline'] ?? null,
                'location' => $data['location'] ?? null,
                'bio' => $data['bio'] ?? null,
            ]);

            CandidateProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'headline' => $data['headline'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'location' => $data['location'] ?? null,
                    'desired_role' => $data['desired_role'] ?? null,
                    'experience_years' => $data['experience_years'] ?? null,
                    'skills' => array_values(array_filter(array_map('trim', explode(',', $data['skills'] ?? '')))),
                    'salary_expectation' => $data['salary_expectation'] ?? null,
                    'portfolio_url' => $data['portfolio_url'] ?? null,
                    'summary' => $data['bio'] ?? null,
                ]
            );
        }

        return back()->with('status', 'Profil mis à jour.');
    }
}
