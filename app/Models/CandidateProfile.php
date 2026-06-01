<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateProfile extends Model
{
    protected $fillable = [
        'user_id',
        'headline',
        'phone',
        'location',
        'desired_role',
        'experience_years',
        'skills',
        'salary_expectation',
        'portfolio_url',
        'summary',
    ];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
