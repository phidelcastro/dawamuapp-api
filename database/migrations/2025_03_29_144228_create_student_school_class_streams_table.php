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
        Schema::create('student_school_class_streams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('school_class_stream_id')->constrained('school_class_streams');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status',['ACTIVE','PROMOTED','DROPPED','EXPELLED','TRANSFERRED','GRADUATED']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_school_class_streams');
    }
};
