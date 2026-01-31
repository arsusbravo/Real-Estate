<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_viewings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete();

            $table->date('scheduled_date');
            $table->time('scheduled_time');
            $table->string('status')->default('scheduled'); // scheduled, confirmed, completed, cancelled, no_show

            $table->text('notes')->nullable();
            $table->text('feedback')->nullable(); // Buyer's feedback after viewing
            $table->unsignedTinyInteger('rating')->nullable(); // 1-5

            $table->timestamps();

            // Indexes
            $table->index(['property_id', 'scheduled_date']);
            $table->index(['buyer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_viewings');
    }
};
