<?php

use App\Enums\UserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('whatsapp_number')->nullable()->after('phone');
            $table->string('ktp_number', 16)->nullable()->after('whatsapp_number');
            $table->text('address')->nullable()->after('ktp_number');
            $table->string('role')->default(UserRole::BUYER->value)->after('address');
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            $table->string('avatar_path')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'whatsapp_number',
                'ktp_number',
                'address',
                'role',
                'phone_verified_at',
                'avatar_path',
            ]);
        });
    }
};
