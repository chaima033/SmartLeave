<?php

use App\Http\Controllers\AiAssistantController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobOfferController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResumeController;
use App\Models\JobOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

const RESUME_ROUTE = '/resumes/{resume}';

Route::get('/', function (Request $request) {
    if ($request->user()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'createLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'storeLogin'])->name('login.store');
    Route::get('/register', [AuthController::class, 'createRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'storeRegister'])->name('register.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/assistant', [AiAssistantController::class, 'index'])->name('assistant.index');
    Route::post('/assistant', [AiAssistantController::class, 'ask'])->name('assistant.ask');

    Route::get('/jobs', [JobOfferController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/{jobOffer:slug}', [JobOfferController::class, 'show'])->name('jobs.show');
});

Route::middleware(['auth', 'role:candidate'])->group(function (): void {
    Route::get('/resumes', [ResumeController::class, 'index'])->name('resumes.index');
    Route::get('/resumes/create', [ResumeController::class, 'create'])->name('resumes.create');
    Route::post('/resumes', [ResumeController::class, 'store'])->name('resumes.store');

    Route::get(RESUME_ROUTE, [ResumeController::class, 'edit'])->name('resumes.edit');
    Route::put(RESUME_ROUTE, [ResumeController::class, 'update'])->name('resumes.update');
    Route::delete(RESUME_ROUTE, [ResumeController::class, 'destroy'])->name('resumes.destroy');

    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');

    Route::get('/jobs/{jobOffer:slug}/apply', function (JobOffer $jobOffer) {
        return redirect()
            ->route('jobs.show', $jobOffer)
            ->with('status', 'Utilisez le formulaire de candidature pour envoyer votre dossier.');
    })->name('jobs.apply.form');

    Route::post('/jobs/{jobOffer:slug}/apply', [ApplicationController::class, 'store'])
        ->name('jobs.apply');
});

Route::middleware(['auth', 'role:recruiter'])->group(function (): void {
    Route::get('/recruiter/jobs', [JobOfferController::class, 'manage'])
        ->name('recruiter.jobs.index');

    Route::get('/recruiter/jobs/create', [JobOfferController::class, 'create'])
        ->name('recruiter.jobs.create');

    Route::post('/recruiter/jobs', [JobOfferController::class, 'store'])
        ->name('recruiter.jobs.store');

    Route::get('/recruiter/jobs/{jobOffer:slug}/edit', [JobOfferController::class, 'edit'])
        ->name('recruiter.jobs.edit');

    Route::put('/recruiter/jobs/{jobOffer:slug}', [JobOfferController::class, 'update'])
        ->name('recruiter.jobs.update');

    Route::delete('/recruiter/jobs/{jobOffer:slug}', [JobOfferController::class, 'destroy'])
        ->name('recruiter.jobs.destroy');

    Route::get('/recruiter/applications', [ApplicationController::class, 'recruiterIndex'])
        ->name('recruiter.applications.index');

    Route::put('/recruiter/applications/{application}', [ApplicationController::class, 'update'])
        ->name('recruiter.applications.update');

    Route::get('/recruiter/cvs', [ResumeController::class, 'recruiterIndex'])
        ->name('recruiter.resumes.index');

    Route::get('/recruiter/cvs/{user}', [ResumeController::class, 'recruiterShow'])
        ->name('recruiter.resumes.show');
});
