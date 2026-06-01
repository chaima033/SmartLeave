<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Application;

class JobOffer extends Model
{
    protected $fillable = [
        'recruiter_id',
        'company_profile_id',
        'title',
        'slug',
        'location',
        'contract_type',
        'work_mode',
        'salary_min',
        'salary_max',
        'currency',
        'description',
        'responsibilities',
        'requirements',
        'skills',
        'status',
        'published_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $jobOffer): void {
            if (! $jobOffer->slug && $jobOffer->title) {
                $jobOffer->slug = Str::slug($jobOffer->title.'-'.$jobOffer->recruiter_id.'-'.Str::random(6));
            }

            if ($jobOffer->status === 'published' && ! $jobOffer->published_at) {
                $jobOffer->published_at = now();
            }
        });
    }

    public function recruiter()
    {
        return $this->belongsTo(User::class, 'recruiter_id');
    }

    public function companyProfile()
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
