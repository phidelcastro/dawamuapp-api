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
        Schema::create('school_class_streams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_class_id')->constrained('school_classes');
            $table->string('stream_name');
            $table->string('stream_code')->nullable();
            $table->string('stream_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_class_streams');
    }
};
