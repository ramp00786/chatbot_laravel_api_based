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
        Schema::table('chatbot_questions', function (Blueprint $table) {
            $table->json('answer_data')->nullable()->after('answer'); // replace 'last_column_name' with the actual last column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chatbot_questions', function (Blueprint $table) {
            $table->dropColumn('answer_data');
        });
    }
};
