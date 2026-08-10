<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_events', function (Blueprint $table): void {
            $table->id();
            $table->string('provider')->index();
            $table->string('event')->index();
            $table->string('event_id');
            $table->string('payment_reference')->nullable()->index();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->json('payload');
            $table->string('processing_status')->default('received')->index();
            $table->text('message')->nullable();
            $table->timestamp('processed_at')->nullable()->index();
            $table->timestamps();

            $table->unique(['provider', 'event_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_events');
    }
};
