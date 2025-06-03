<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_one')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_two')->constrained('users')->onDelete('cascade');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            // Assurer l'unicité d'une conversation entre deux utilisateurs
            $table->unique(['user_one', 'user_two']);
        });

        // Ajouter la relation conversation aux messages
        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('conversation_id')->nullable()->after('team_id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
            $table->dropColumn('conversation_id');
        });

        Schema::dropIfExists('conversations');
    }
};
