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
        Schema::table('leave_types', function (Blueprint $table) {
            $table->enum('allocation_type', ['fixed', 'accrual'])->default('fixed');
            $table->enum('validation_type', ['no', 'manager', 'hr', 'both'])->default('manager');
            $table->enum('request_unit', ['day', 'half_day', 'hour'])->default('day');
            $table->boolean('allow_half_day')->default(false);
            $table->boolean('allow_hour')->default(false);
            $table->decimal('accrual_rate', 6, 2)->default(0);
            $table->decimal('accrual_cap', 6, 2)->nullable();
            $table->boolean('requires_allocation')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->dropColumn([
                'allocation_type',
                'validation_type',
                'request_unit',
                'allow_half_day',
                'allow_hour',
                'accrual_rate',
                'accrual_cap',
                'requires_allocation',
            ]);
        });
    }
};
