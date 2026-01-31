<?php

use App\Enums\TransactionStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('transaction_number')->unique(); // TRX-YYYYMM-XXXX

            // Parties
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('notary_id')->nullable()->constrained('notary_partners')->nullOnDelete();
            $table->foreignId('assigned_admin_id')->nullable()->constrained('users')->nullOnDelete();

            // Financial
            $table->decimal('agreed_price', 15, 2)->nullable();
            $table->decimal('dp_amount', 15, 2)->nullable();
            $table->decimal('commission_percentage', 5, 2)->default(2.50);
            $table->decimal('commission_amount', 15, 2)->nullable();

            // Status tracking
            $table->string('status')->default(TransactionStatus::INQUIRY->value);
            $table->unsignedTinyInteger('current_step')->default(1);

            // Key dates
            $table->timestamp('inquiry_date')->nullable();
            $table->timestamp('viewing_date')->nullable();
            $table->timestamp('negotiation_started_at')->nullable();
            $table->timestamp('agreement_date')->nullable();
            $table->timestamp('dp_paid_at')->nullable();
            $table->timestamp('notary_assigned_at')->nullable();
            $table->timestamp('ajb_signed_at')->nullable();
            $table->timestamp('certificate_transferred_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->text('cancellation_reason')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index('transaction_number');
            $table->index(['buyer_id', 'status']);
            $table->index(['seller_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
