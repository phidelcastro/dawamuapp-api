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
        Schema::create('school_class_stream_teacher_subjects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("teacher_subject_id");
            $table->unsignedBigInteger("school_class_stream_id");
            $table->enum("is_stream_class_teacher",['Yes','No']);
            $table->date("start_date");
            $table->date("end_date");
            $table->enum("current_status",['Active','Inactive','Transferred Internally','Transferred Externally']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_class_stream_teacher_subjects');
    }
};
