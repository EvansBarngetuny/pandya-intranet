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
        //
          Schema::table('events', function (Blueprint $table) {
            // Check and add missing columns
            if (!Schema::hasColumn('events', 'type')) {
                $table->enum('type', ['training', 'meeting', 'cme', 'social', 'other'])->default('meeting')->after('description');
            }

            if (!Schema::hasColumn('events', 'venue')) {
                $table->string('venue')->nullable()->after('type');
            }

            if (!Schema::hasColumn('events', 'start_datetime')) {
                $table->datetime('start_datetime')->nullable()->after('venue');
            }

            if (!Schema::hasColumn('events', 'end_datetime')) {
                $table->datetime('end_datetime')->nullable()->after('start_datetime');
            }

            if (!Schema::hasColumn('events', 'target_departments')) {
                $table->json('target_departments')->nullable()->after('end_datetime');
            }

            if (!Schema::hasColumn('events', 'organizer_id')) {
                $table->foreignId('organizer_id')->nullable()->constrained('users')->after('target_departments');
            }

            if (!Schema::hasColumn('events', 'contact_person')) {
                $table->string('contact_person')->nullable()->after('organizer_id');
            }

            if (!Schema::hasColumn('events', 'contact_phone')) {
                $table->string('contact_phone')->nullable()->after('contact_person');
            }

            if (!Schema::hasColumn('events', 'requires_registration')) {
                $table->boolean('requires_registration')->default(false)->after('contact_phone');
            }

            if (!Schema::hasColumn('events', 'max_attendees')) {
                $table->integer('max_attendees')->nullable()->after('requires_registration');
            }
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
              Schema::table('events', function (Blueprint $table) {
            $columns = ['type', 'venue', 'start_datetime', 'end_datetime', 'target_departments',
                       'organizer_id', 'contact_person', 'contact_phone', 'requires_registration', 'max_attendees'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('events', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
