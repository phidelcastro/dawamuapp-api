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
            Schema::create('parent_school_parent_messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_parent_message_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->constrained('guardians')->onDelete('cascade');

            $table->boolean('delivered_via_email')->default(false);
            $table->timestamp('email_delivery_time')->nullable();

            $table->boolean('delivered_via_phone')->default(false);
            $table->timestamp('phone_delivery_time')->nullable();

            $table->boolean('delivered_via_push')->default(false);
            $table->timestamp('push_delivery_time')->nullable();

            $table->text('parent_comment')->nullable();
            $table->json('response_logs')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_school_parent_messages');
    }
};
