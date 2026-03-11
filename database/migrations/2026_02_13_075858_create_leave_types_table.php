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
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('color')->nullable();
            $table->boolean('is_paid')->default(true);
            $table->boolean('requires_manager_approval')->default(true);
            $table->boolean('requires_hr_approval')->default(false);
            $table->boolean('carry_forward')->default(false);
            $table->unsignedInteger('carry_forward_cap')->default(0);
            $table->unsignedInteger('max_days_per_request')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
