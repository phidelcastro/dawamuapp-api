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
        Schema::create('student_medical_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->date('date');
            $table->text('complaint');
            $table->text('procedure')->nullable();
            $table->string('medicines')->nullable();
            $table->date('next_checkup_date')->nullable();
            $table->boolean('alert_parent')->default(false);
            $table->boolean('refer_external')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_medical_histories');
    }
};
