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
        Schema::create('chat_logs', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->enum('sender', ['bot', 'user']);
            $table->string('type')->nullable(); // question, input, answer, final_answer
            $table->text('message');
            $table->unsignedBigInteger('parent_id')->nullable(); // optional for nesting
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_logs');
    }
};
