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
        Schema::table('student_school_exam_school_class_school_class_streams', function (Blueprint $table) {
            //
            $table->unsignedBigInteger("student_id");
            $table->double('score')->nullable()->change();
            $table->double('percentage_score')->nullable()->change();
            $table->unsignedBigInteger('grade_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_school_exam_school_class_school_class_streams', function (Blueprint $table) {
            //
            $table->dropColumn("student_id");
            $table->double('score')->nullable(false)->change();
            $table->double('percentage_score')->nullable(false)->change();
            $table->unsignedBigInteger('grade_id')->nullable(false)->change();
            
        });
    }
};
