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
        Schema::create('leave_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leave_type_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version')->default(1);
            $table->unsignedSmallInteger('min_service_months')->default(0);
            $table->decimal('max_days_per_year', 6, 2)->nullable();
            $table->decimal('max_unpaid_days_per_year', 6, 2)->nullable();
            $table->boolean('allow_backdate')->default(false);
            $table->unsignedSmallInteger('allow_future_apply_days')->nullable();
            $table->boolean('yearly_reset')->default(true);
            $table->unsignedSmallInteger('expiry_days')->nullable();
            $table->decimal('carry_forward_limit', 6, 2)->nullable();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['leave_type_id', 'version']);
            $table->index(['leave_type_id', 'is_active', 'effective_from']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_policies');
    }
};
