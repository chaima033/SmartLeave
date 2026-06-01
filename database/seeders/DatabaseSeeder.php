<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\CompanyProfile;
use App\Models\JobOffer;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $recruiter = User::factory()->create([
            'name' => 'Amina Traore',
            'email' => 'recrutement@novalink.io',
            'role' => 'recruiter',
            'phone' => '+33 1 88 00 11 22',
            'headline' => 'Head of Talent Acquisition',
            'location' => 'Paris',
            'bio' => 'Recrutement produit, tech et data.',
            'company_name' => 'NovaLink',
            'company_industry' => 'Technology',
            'company_website' => 'https://novalink.io',
            'company_size' => '51-200',
            'company_description' => 'Equipe produit rapide orientee impact et apprentissage.',
        ]);

        $candidate = User::factory()->create([
            'name' => 'Karim Benali',
            'email' => 'karim@example.com',
            'role' => 'candidate',
            'phone' => '+33 6 12 34 56 78',
            'headline' => 'Developpeur Laravel / React',
            'location' => 'Lyon',
            'bio' => 'Je construis des produits web utiles et robustes.',
        ]);

        $companyProfile = CompanyProfile::create([
            'user_id' => $recruiter->id,
            'company_name' => 'NovaLink',
            'industry' => 'Technology',
            'website' => 'https://novalink.io',
            'size' => '51-200',
            'location' => 'Paris',
            'contact_email' => 'recrutement@novalink.io',
            'description' => 'Equipe produit rapide orientee impact et apprentissage.',
            'vision' => 'Recruter des talents capables de livrer vite et bien.',
        ]);

        CandidateProfile::create([
            'user_id' => $candidate->id,
            'headline' => 'Developpeur Laravel / React',
            'phone' => '+33 6 12 34 56 78',
            'location' => 'Lyon',
            'desired_role' => 'Full stack developer',
            'experience_years' => 4,
            'skills' => ['Laravel', 'PHP', 'React', 'Tailwind', 'MySQL'],
            'salary_expectation' => '45k-55k EUR',
            'portfolio_url' => 'https://portfolio.example.com',
            'summary' => 'Developpeur full stack avec experience sur des produits SaaS.',
        ]);

        $resume = Resume::create([
            'user_id' => $candidate->id,
            'title' => 'CV principal - Karim Benali',
            'summary' => 'Developpeur full stack orientee produit.',
            'experience' => '4 ans sur Laravel, React et APIs.',
            'education' => 'Licence informatique.',
            'skills' => ['Laravel', 'React', 'API REST', 'MySQL', 'CI/CD'],
            'projects' => 'Plateforme RH, dashboard metier, outils internes.',
            'certifications' => 'Google Cloud fundamentals.',
            'languages' => 'Français, Anglais',
            'is_primary' => true,
        ]);

        $jobOne = JobOffer::create([
            'recruiter_id' => $recruiter->id,
            'company_profile_id' => $companyProfile->id,
            'title' => 'Developpeur Laravel Senior',
            'slug' => 'developpeur-laravel-senior',
            'location' => 'Paris',
            'contract_type' => 'CDI',
            'work_mode' => 'Hybride',
            'salary_min' => '50000',
            'salary_max' => '65000',
            'currency' => 'EUR',
            'description' => 'Construire et faire evoluer des modules metier critiques.',
            'responsibilities' => 'Concevoir, developper et livrer des fonctionnalites de bout en bout.',
            'requirements' => 'Laravel, APIs, tests, SQL, sens produit.',
            'skills' => ['Laravel', 'PHP', 'SQL', 'Tests'],
            'status' => 'published',
            'expires_at' => now()->addDays(30),
        ]);

        JobOffer::create([
            'recruiter_id' => $recruiter->id,
            'company_profile_id' => $companyProfile->id,
            'title' => 'Product Designer UX/UI',
            'slug' => 'product-designer-ux-ui',
            'location' => 'Remote',
            'contract_type' => 'Freelance',
            'work_mode' => 'Remote',
            'salary_min' => '400',
            'salary_max' => '550',
            'currency' => 'EUR',
            'description' => 'Designer des interfaces claires et efficaces pour la plateforme.',
            'responsibilities' => 'Maquettes, design system, iterations avec produit.',
            'requirements' => 'Figma, prototypage, collaboration equipe.',
            'skills' => ['Figma', 'UX', 'UI', 'Design System'],
            'status' => 'published',
            'expires_at' => now()->addDays(21),
        ]);

        Application::create([
            'job_offer_id' => $jobOne->id,
            'candidate_id' => $candidate->id,
            'resume_id' => $resume->id,
            'status' => 'reviewing',
            'cover_letter' => 'Je souhaite rejoindre NovaLink pour travailler sur des produits web exigeants.',
            'resume_snapshot' => $resume->toArray(),
            'candidate_notes' => 'Disponible sous 1 mois.',
            'recruiter_feedback' => 'Profil technique solide, a rappeler.',
        ]);
    }
}
