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
        Schema::create('guardian_event_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guardian_event_id')->constrained()->onDelete('cascade');
            $table->foreignId('guardian_id')->constrained('guardians')->onDelete('cascade');
            $table->enum('phone_delivery_status', ['not_sent', 'sent', 'failed'])->default('not_sent');
            $table->enum('email_delivery_status', ['not_sent', 'sent', 'failed'])->default('not_sent');
            $table->enum('push_status', ['not_sent', 'sent', 'failed'])->default('not_sent');
            $table->enum('status', ['pending', 'confirmed', 'declined'])->default('pending');
            $table->timestamp('responded_at')->nullable();
            $table->text('comment')->nullable();
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
