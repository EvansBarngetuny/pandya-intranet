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
        Schema::table('departments', function (Blueprint $table) {
            //
           // $table->renameColumn('hod_name', 'head_of_department');
            $table->string('icon')->default('🏥')->after('head_of_department');
            $table->string('color')->default('#3B82F6')->after('icon');
            $table->integer('staff_count')->default(0)->after('color');
            $table->boolean('is_active')->default(true)->after('staff_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            //
           // $table->renameColumn('head_of_department', 'hod_name');

            $table->dropColumn([
                'icon',
                'color',
                'staff_count',
                'is_active',
            ]);

        });
    }
};
