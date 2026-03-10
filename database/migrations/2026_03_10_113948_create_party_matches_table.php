<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('party_matches', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user1_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('user2_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('party_id')->constrained()->cascadeOnDelete();
    $table->timestamp('created_at')->useCurrent();
    $table->unique(['user1_id', 'user2_id', 'party_id']);
});
    }

    public function down(): void
    {
        Schema::dropIfExists('party_matches');
    }
};