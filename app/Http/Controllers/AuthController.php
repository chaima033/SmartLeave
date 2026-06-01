<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\StoreLoginRequest;
use App\Http\Requests\Auth\StoreRegisterRequest;
use App\Models\CandidateProfile;
use App\Models\CompanyProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function createLogin(): View
    {
        return view('auth.login');
    }

    public function storeLogin(StoreLoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (! Auth::attempt($credentials, $request->boolean('remember')))
        {
            return back()->withErrors([
                'email' => 'Identifiants invalides.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function createRegister(): View
    {
        return view('auth.register');
    }

    public function storeRegister(StoreRegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'phone' => $data['phone'] ?? null,
            'headline' => $data['headline'] ?? null,
            'location' => $data['location'] ?? null,
            'bio' => $data['bio'] ?? null,
            'company_name' => $data['company_name'] ?? null,
            'company_industry' => $data['company_industry'] ?? null,
            'company_website' => $data['company_website'] ?? null,
            'company_size' => $data['company_size'] ?? null,
            'company_description' => $data['company_description'] ?? null,
        ]);

        if ($user->role === 'candidate')
        {
            CandidateProfile::create([
                'user_id' => $user->id,
                'headline' => $user->headline,
                'phone' => $user->phone,
                'location' => $user->location,
            ]);
        }
        else
        {
            CompanyProfile::create([
                'user_id' => $user->id,
                'company_name' => $user->company_name ?: $user->name,
                'industry' => $user->company_industry,
                'website' => $user->company_website,
                'size' => $user->company_size,
                'location' => $user->location,
                'description' => $user->company_description,
            ]);
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
