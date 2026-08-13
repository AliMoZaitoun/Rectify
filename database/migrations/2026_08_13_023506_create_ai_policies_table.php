<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_policies', function (Blueprint $table) {
            $table->id();
            $table->string('tone_of_voice')->default('professional');

            $table->text('legal_guidelines')->nullable();

            $table->text('compensation_guidelines')->nullable();

            $table->text('general_instructions')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_policies');
    }
};
