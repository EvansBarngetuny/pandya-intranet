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
        Schema::table('documents', function (Blueprint $table) {
            //
             if (!Schema::hasColumn('documents', 'require_acknowledgment')) {
                $table->boolean('require_acknowledgment')->default(false)->after('is_active');
             }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            //
               if (Schema::hasColumn('documents', 'require_acknowledgment')) {
                $table->dropColumn('require_acknowledgment');
            }
        });
    }
};
