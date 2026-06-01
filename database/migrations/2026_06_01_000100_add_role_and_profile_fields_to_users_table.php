<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role')->default('candidate')->after('password');
            $table->string('phone')->nullable()->after('role');
            $table->string('headline')->nullable()->after('phone');
            $table->string('location')->nullable()->after('headline');
            $table->longText('bio')->nullable()->after('location');
            $table->string('company_name')->nullable()->after('bio');
            $table->string('company_industry')->nullable()->after('company_name');
            $table->string('company_website')->nullable()->after('company_industry');
            $table->string('company_size')->nullable()->after('company_website');
            $table->longText('company_description')->nullable()->after('company_size');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
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
            ]);
        });
    }
};
