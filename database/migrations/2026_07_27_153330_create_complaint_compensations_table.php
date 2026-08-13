<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_compensations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('approved_by_id')->nullable()->constrained('employees');
            $table->string('type');
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('coupon_code')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('granted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_compensations');
    }
};
