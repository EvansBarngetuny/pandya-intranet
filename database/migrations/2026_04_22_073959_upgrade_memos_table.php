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
            $table->longText('content')->change();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])
                  ->default('medium')
                  ->change();
            $table->enum('status', ['draft', 'published', 'archived'])
                  ->default('draft')
                  ->change();
            $table->renameColumn('expiry_date', 'expires_at');
           $table->enum('audience_type', ['all', 'departments', 'specific_users'])
                  ->default('all')
                  ->after('priority');
            $table->json('audience_ids')->nullable()->after('audience_type');
            $table->boolean('require_acknowledgment')
                  ->default(true)
                  ->after('audience_ids');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('memos', function (Blueprint $table) {
            //
            $table->text('content')->change();

            $table->enum('priority', ['Low', 'Medium', 'High', 'urgent'])
                  ->default('Medium')
                  ->change();

            $table->enum('status', ['Draft', 'Published', 'Archived'])
                  ->default('Draft')
                  ->change();

            $table->renameColumn('expires_at', 'expiry_date');

            $table->dropColumn([
                'audience_type',
                'audience_ids',
                'require_acknowledgment',
                ]);
        });
    }
};
