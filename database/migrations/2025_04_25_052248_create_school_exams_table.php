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
        Schema::create('school_exams', function (Blueprint $table) {
            $table->id();
            $table->string("exam_label");
            $table->dateTime("start_date");
            $table->dateTime("end_date");
            $table->enum("exam_status",['Active','Inactive','Ongoing','completed','Canceled']);
            $table->string("note")->nullable();
            $table->enum("target",['school','Specific Class','Specific Classes']);
            $table->enum("exam_type",['CAT','END OF TERM','OPENING EXAM','MID TERM EXAM','RANDOM EVALUATION ','OTHER EXAM']);
            $table->unsignedBigInteger("school_term");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_exams');
    }
};
