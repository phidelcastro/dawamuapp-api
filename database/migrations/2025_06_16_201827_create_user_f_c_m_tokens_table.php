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
        Schema::create('user_f_c_m_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("user_id");
            $table->text("token");
            $table->text("phone_type")->nullable();
            $table->enum("status",['Active','InActive','Expired'])->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_f_c_m_tokens');
    }
};
