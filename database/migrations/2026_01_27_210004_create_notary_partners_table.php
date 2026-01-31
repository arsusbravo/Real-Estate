<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notary_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('license_number')->nullable(); // SK Notaris
            $table->string('office_name')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('city')->default('Jakarta');
            $table->json('specializations')->nullable(); // PPAT, Notaris, etc.
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notary_partners');
    }
};
