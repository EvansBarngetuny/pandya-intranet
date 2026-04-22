<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
             $table->string('title');
            $table->text('description');
            $table->enum('type', ['training', 'meeting', 'cme', 'social', 'other'])->default('meeting');
            $table->string('venue');
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->json('target_departments')->nullable();
            $table->foreignId('organizer_id')->constrained('users');
            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->boolean('requires_registration')->default(false);
            $table->integer('max_attendees')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
