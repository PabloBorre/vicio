<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hacemos email y password opcionales (login por sesión de fiesta)
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
            
            // Campos de VicioApp
            $table->string('username')->nullable()->after('name');
            $table->string('profile_photo_path')->nullable()->after('username');
            $table->unsignedTinyInteger('age')->nullable()->after('profile_photo_path');
            $table->string('gender_identity')->nullable()->after('age');
            $table->string('sexual_preference')->nullable()->after('gender_identity');
            $table->text('bio')->nullable()->after('sexual_preference');
            $table->boolean('is_admin')->default(false)->after('bio');
            $table->boolean('is_banned')->default(false)->after('is_admin');
            $table->foreignId('current_party_id')->nullable()->after('is_banned')
                ->constrained('parties')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username', 'profile_photo_path', 'age',
                'gender_identity', 'sexual_preference', 'bio',
                'is_admin', 'is_banned', 'current_party_id'
            ]);
        });
    }
};