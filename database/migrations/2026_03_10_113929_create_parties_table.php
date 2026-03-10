<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('qr_code')->unique(); // UUID único para el QR
            $table->datetime('starts_at');        // Hora de inicio del swipe
            $table->datetime('registration_opens_at')->nullable();
            $table->datetime('registration_closes_at')->nullable();
            $table->enum('status', ['draft', 'registration', 'countdown', 'active', 'finished'])
                  ->default('draft');
            $table->string('cover_image')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parties');
    }
};