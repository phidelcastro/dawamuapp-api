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
        Schema::create('school_exam_school_class_school_class_streams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("school_exam_school_class_id");
            $table->unsignedBigInteger("school_class_stream_id");
            $table->enum("status",['Active','Inactive']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_exam_school_class_school_class_streams');
    }
};
