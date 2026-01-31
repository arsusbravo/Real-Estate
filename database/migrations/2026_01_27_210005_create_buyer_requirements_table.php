<?php

use App\Enums\ListingType;
use App\Enums\RequirementUrgency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buyer_requirements', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Property preferences
            $table->json('property_types'); // Array of PropertyType values
            $table->string('listing_type')->default(ListingType::DIJUAL->value);

            // Budget
            $table->decimal('min_price', 15, 2)->nullable();
            $table->decimal('max_price', 15, 2)->nullable();

            // Size preferences
            $table->integer('min_land_area')->nullable();
            $table->integer('max_land_area')->nullable();
            $table->integer('min_building_area')->nullable();
            $table->integer('max_building_area')->nullable();

            // Specifications
            $table->unsignedTinyInteger('min_bedrooms')->nullable();
            $table->unsignedTinyInteger('min_bathrooms')->nullable();

            // Location preferences
            $table->json('preferred_locations')->nullable(); // Array of city/district
            $table->json('preferred_certificate_types')->nullable();

            // Timeline
            $table->string('urgency')->default(RequirementUrgency::FLEKSIBEL->value);

            // Additional info
            $table->text('additional_notes')->nullable();
            $table->string('status')->default('active'); // active, matched, closed

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buyer_requirements');
    }
};
