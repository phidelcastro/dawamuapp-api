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
        Schema::create('student_disciplines', function (Blueprint $table) {
            $table->id();
            $table->string('location');
            $table->text('offense');
            $table->text('action_taken');
            $table->boolean('parent_notification')->default(false);
            $table->text('follow_up');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('reported_by');        
            $table->text('notes')->nullable();
            $table->enum('status',['Reported','Resolved'])->default('reported');
            $table->enum('severity',['Mild','Moderate','severe'])->default('Moderate');
            $table->json('images')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_disciplines');
    }
};
