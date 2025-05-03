<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->json('location_json')->nullable()->after('last_column_name'); // replace 'last_column_name' with the actual last column
        });
    }

    public function down(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->dropColumn('location_json');
        });
    }
};
