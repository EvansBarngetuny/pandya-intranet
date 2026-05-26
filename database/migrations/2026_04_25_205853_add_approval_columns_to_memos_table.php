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
        Schema::table('memos', function (Blueprint $table) {
            //
            if (!Schema::hasColumn('memos', 'published_by')) {
                $table->foreignId('published_by')->nullable()->constrained('users')->after('published_at');
            }
            if (!Schema::hasColumn('memos', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('published_by');
            }
            if (!Schema::hasColumn('memos', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->constrained('users')->after('approved_at');
            }
            if (!Schema::hasColumn('memos', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('approved_by');
            }

            // Modify status enum to include new values
            DB::statement("ALTER TABLE memos MODIFY status ENUM('draft', 'pending_approval', 'published', 'rejected', 'archived') DEFAULT 'draft'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('memos', function (Blueprint $table) {
            //
            $table->dropColumn(['published_by', 'approved_at', 'approved_by', 'rejection_reason']);
            DB::statement("ALTER TABLE memos MODIFY status ENUM('draft', 'published', 'archived') DEFAULT 'draft'");
        });
    }
};
