<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('swipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('swiper_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('swiped_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('party_id')->constrained()->cascadeOnDelete();
            $table->enum('direction', ['like', 'dislike']);
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['swiper_id', 'swiped_id', 'party_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('swipes');
    }
};