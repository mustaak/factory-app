<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vendor_id')
                ->constrained('vendors')
                ->cascadeOnDelete();

            $table->string('name', 200);
            $table->string('slug', 220)->unique();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();

            // Optional brand
            $table->string('brand', 100)->nullable();

            // Product listing status
            $table->enum('status', [
                'active',
                'inactive',
            ])->default('inactive');

            // Admin approval workflow
            $table->enum('approval_status', [
                'pending',
                'approved',
                'rejected',
            ])->default('pending');

            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'approval_status',
                'status',
            ]);

            $table->index('vendor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
