<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained()->cascadeOnDelete();

            $table->string('old_status')->nullable();
            $table->string('new_status');

            $table->foreignId('assigned_to_id')->nullable()->constrained('employees')->nullOnDelete();

            $table->nullableMorphs('changed_by');

            $table->unsignedInteger('duration_in_hours')->nullable()->default(0);

            $table->text('comment')->nullable();

            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_histories');
    }
};
