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
        Schema::create('student_school_exam_school_class_school_class_streams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("school_exam_school_class_subject_id");
            $table->unsignedBigInteger('school_exam_school_class_school_class_streams_id');
            $table->double("score");
            $table->double("percentage_score");
            $table->unsignedBigInteger("grade_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_school_exam_school_class_school_class_streams');
    }
};
