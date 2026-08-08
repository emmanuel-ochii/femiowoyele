<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pickup_points', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('city')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('opening_hours')->nullable();
            $table->string('note')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            // Public identifier shown to the buyer and used as the payment
            // provider's reference. Never expose the auto-increment id.
            $table->string('reference')->unique();
            $table->foreignId('book_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('pickup_point_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name');
            $table->string('email')->index();
            $table->string('phone')->nullable();
            $table->unsignedSmallInteger('quantity')->default(1);

            // Money is stored in minor units (kobo) as integers. Paystack works
            // in kobo, and integers avoid float rounding on totals.
            $table->unsignedInteger('unit_amount');
            $table->unsignedInteger('total_amount');
            $table->string('currency', 3)->default('NGN');

            $table->string('status')->default('pending')->index();
            $table->string('payment_provider')->default('mock');
            $table->string('payment_reference')->nullable()->index();
            $table->timestamp('paid_at')->nullable();
            $table->json('payment_meta')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('pickup_points');
    }
};
