<?php

use App\Enums\DocumentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Polymorphic relation (Transaction, Property, User)
            $table->morphs('documentable');

            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->string('document_type');
            $table->string('category'); // seller_document, buyer_document, property_document, transaction_document

            // File info
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('mime_type')->nullable();

            // Verification
            $table->string('status')->default(DocumentStatus::UPLOADED->value);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes (morphs already creates index on documentable_type, documentable_id)
            $table->index('status');
            $table->index('document_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
