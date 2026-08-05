<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rsvps', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            // Unique: an RSVP is a person's current answer, not a log of
            // submissions, so re-submitting updates rather than duplicating.
            $table->string('email')->unique();
            $table->boolean('attending')->default(true)->index();
            $table->unsignedTinyInteger('guests')->default(0);
            $table->text('note')->nullable();
            $table->string('event_slug')->default('home.launch')->index();
            $table->string('source')->default('website');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rsvps');
    }
};
