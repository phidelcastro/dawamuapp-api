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
        Schema::create('other_contacts', function (Blueprint $table) {
        $table->id();
        $table->string('first_name');
        $table->string('last_name');
        $table->string('relationship')->nullable();
        $table->string('phone');
        $table->string('email');
        $table->unsignedBigInteger('student_id');
        $table->unsignedBigInteger('guardian_id');        
        $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_contacts');
    }
};
