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
        Schema::create('guardian_events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date');
            $table->date('confirm_by')->nullable();
            $table->text('requirements')->nullable();
            $table->string('location')->nullable();
            $table->json('notification_types');
            $table->enum('reminder_frequency', ['once', 'daily', 'weekly', 'daily_x_days_before'])->nullable();
            $table->integer('reminder_days_before')->nullable(); 
            $table->date('remind_until')->nullable();
            $table->json('parents');
            $table->string('banner_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardian_events');
    }
};
