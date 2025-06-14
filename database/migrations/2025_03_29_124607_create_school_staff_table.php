<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('school_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->date('date_of_employment')->nullable();
            $table->string('staff_id')->nullable();
            $table->string('professional_registration_number')->nullable();
            $table->string('level_of_education')->nullable();
            $table->integer('years_of_experience_prior_employment')->nullable();
            $table->enum('status', ['ACTIVE', 'SUSPENDED', 'CLOSED']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_staff');
    }
};
