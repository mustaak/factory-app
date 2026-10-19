
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            // Business information
            $table->string('business_name', 150);

            // Contact information
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('country_code', 10)->nullable();

            // Business address
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 100)->nullable();

            // Vendor verification
            $table->enum('verification_status', [
                'pending',
                'approved',
                'rejected',
            ])->default('pending');

            // Vendor account status
            $table->enum('status', [
                'active',
                'suspended',
                'inactive',
            ])->default('inactive');

            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
