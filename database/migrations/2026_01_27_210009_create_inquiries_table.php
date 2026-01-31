<?php

use App\Enums\InquiryStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Nullable for guest inquiries

            // Contact info (for guests or overriding user info)
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->text('message');
            $table->string('preferred_contact_method')->default('whatsapp'); // phone, whatsapp, email

            // Status
            $table->string('status')->default(InquiryStatus::NEW->value);
            $table->foreignId('converted_to_transaction_id')->nullable()->constrained('transactions')->nullOnDelete();

            // Admin notes
            $table->text('admin_notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['property_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
