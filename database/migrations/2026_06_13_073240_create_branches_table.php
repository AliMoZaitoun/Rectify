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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('description')->nullable();
            $table->foreignId('location_id')->constrained();
            $table->foreignId('manager_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->decimal('monthly_amount_budget', 10, 2)->default(1000.00)->after('description');
            $table->integer('monthly_points_budget')->default(5000);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
