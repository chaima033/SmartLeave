<?php

namespace App\Models;

use App\Models\AiMessage;
use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\CompanyProfile;
use App\Models\JobOffer;
use App\Models\Resume;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'headline',
        'location',
        'bio',
        'company_name',
        'company_industry',
        'company_website',
        'company_size',
        'company_description',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function candidateProfile()
    {
        return $this->hasOne(CandidateProfile::class);
    }

    public function companyProfile()
    {
        return $this->hasOne(CompanyProfile::class);
    }

    public function resumes()
    {
        return $this->hasMany(Resume::class);
    }

    public function jobOffers()
    {
        return $this->hasMany(JobOffer::class, 'recruiter_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'candidate_id');
    }

    public function aiMessages()
    {
        return $this->hasMany(AiMessage::class);
    }
}
