<?php

use App\Enums\CertificateType;
use App\Enums\FacingDirection;
use App\Enums\FurnishingType;
use App\Enums\ListingType;
use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');

            // Basic Info
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('property_type');
            $table->string('listing_type')->default(ListingType::DIJUAL->value);

            // Pricing
            $table->decimal('price', 15, 2);
            $table->boolean('price_negotiable')->default(true);

            // Size
            $table->integer('land_area')->nullable(); // m2
            $table->integer('building_area')->nullable(); // m2

            // Specifications
            $table->unsignedTinyInteger('bedrooms')->nullable();
            $table->unsignedTinyInteger('bathrooms')->nullable();
            $table->unsignedTinyInteger('floors')->default(1);
            $table->unsignedTinyInteger('parking_spaces')->nullable();
            $table->string('furnishing')->nullable();
            $table->string('facing_direction')->nullable();

            // Location
            $table->string('province')->default('DKI Jakarta');
            $table->string('city');
            $table->string('district')->nullable(); // Kecamatan
            $table->string('subdistrict')->nullable(); // Kelurahan
            $table->text('address');
            $table->string('postal_code', 10)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Certificate Info
            $table->string('certificate_type');
            $table->string('certificate_number')->nullable();
            $table->date('certificate_expiry')->nullable(); // For SHGB

            // Status
            $table->string('status')->default(PropertyStatus::DRAFT->value);
            $table->boolean('featured')->default(false);
            $table->boolean('verified')->default(false);
            $table->text('rejection_reason')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('sold_at')->nullable();

            // Stats
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('inquiries_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['status', 'published_at']);
            $table->index(['property_type', 'listing_type']);
            $table->index(['city', 'district']);
            $table->index('price');
            $table->index('featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
