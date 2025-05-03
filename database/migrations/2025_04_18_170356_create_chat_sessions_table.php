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
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_key_id')->constrained();
            $table->string('user_email');
            $table->string('user_name');
            $table->string('user_mobile')->nullable();
            $table->string('ip_address');
            $table->text('user_agent');
            $table->string('device_type');
            $table->string('location')->nullable();
            $table->string('last_column_name')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_sessions');
    }
};
