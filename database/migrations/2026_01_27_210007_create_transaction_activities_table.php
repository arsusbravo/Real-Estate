<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('activity_type'); // status_change, note_added, document_uploaded, viewing_scheduled, etc.
            $table->text('description');
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->timestamps();

            $table->index(['transaction_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_activities');
    }
};
