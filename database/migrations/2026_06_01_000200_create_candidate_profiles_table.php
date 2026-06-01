<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('headline')->nullable();
            $table->string('phone')->nullable();
            $table->string('location')->nullable();
            $table->string('desired_role')->nullable();
            $table->unsignedSmallInteger('experience_years')->nullable();
            $table->json('skills')->nullable();
            $table->string('salary_expectation')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->longText('summary')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_profiles');
    }
};
