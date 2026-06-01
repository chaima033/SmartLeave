<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'job_offer_id',
        'candidate_id',
        'resume_id',
        'status',
        'cover_letter',
        'resume_snapshot',
        'candidate_notes',
        'recruiter_feedback',
        'selected_at',
    ];

    protected function casts(): array
    {
        return [
            'resume_snapshot' => 'array',
            'selected_at' => 'datetime',
        ];
    }

    public function jobOffer()
    {
        return $this->belongsTo(JobOffer::class);
    }

    public function candidate()
    {
        return $this->belongsTo(User::class, 'candidate_id');
    }

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
}
