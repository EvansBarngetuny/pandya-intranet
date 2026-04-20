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
        Schema::create('memos', function (Blueprint $table) {
            $table->id();
            $table->string('memo_number')->unique();
            $table->string('title');
            $table->text('content');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('department_id')->nullable()->constrained();
            $table->enum('priority', ['Low', 'Medium', 'High','urgent'])->default('Medium');
            $table->date('effective_date');
            $table->date('expiry_date')->nullable();
            $table->json('attachments')->nullable();
            $table->json('recipients')->nullable();
            $table->enum('status', ['Draft', 'Published', 'Archived'])->default('Draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memos');
    }
};
