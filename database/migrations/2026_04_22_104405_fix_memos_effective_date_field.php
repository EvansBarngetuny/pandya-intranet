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
      Schema::table('memos', function (Blueprint $table) {
            // Check and add effective_date if it doesn't exist
            if (!Schema::hasColumn('memos', 'effective_date')) {
                $table->date('effective_date')->nullable()->after('priority');
            }
            
            // Check and add expiry_date if it doesn't exist
            if (!Schema::hasColumn('memos', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('effective_date');
            }
            
            // Check and add audience_type if it doesn't exist
            if (!Schema::hasColumn('memos', 'audience_type')) {
                $table->enum('audience_type', ['all', 'departments', 'specific_users'])->default('all')->after('expiry_date');
            }
            
            // Check and add audience_ids if it doesn't exist
            if (!Schema::hasColumn('memos', 'audience_ids')) {
                $table->json('audience_ids')->nullable()->after('audience_type');
            }
            
            // Check and add recipients if it doesn't exist
            if (!Schema::hasColumn('memos', 'recipients')) {
                $table->json('recipients')->nullable()->after('audience_ids');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('memos', function (Blueprint $table) {
            $columns = ['effective_date', 'expiry_date', 'audience_type', 'audience_ids', 'recipients'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('memos', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    
    }
};
