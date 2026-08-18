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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null');

            $table->string('device_id')->nullable()->index();
            $table->boolean('is_anonymous')->default(false);

            $table->string('tracking_code')->nullable()->unique();

            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('restrict');

            $table->string('title');
            $table->text('description')->nullable();

            $table->string('status')->default('pending');
            $table->string('priority')->default('medium');


            $table->foreignId('parent_id')->nullable()->constrained('complaints')->onDelete('cascade');

            $table->timestamp('sla_due_at')->nullable();
            $table->timestamp('resolved_at')->nullable();

            $table->json('ai_summary')->nullable();
            $table->string('ai_suggested_category')->nullable();
            $table->boolean('is_spam')->default(false);

            $table->integer('reopen_count')->default(0);

            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index('tracking_code');
            $table->index('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
