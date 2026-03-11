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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('days', 6, 2);
            $table->text('reason')->nullable();
            $table->enum('status', ['draft', 'submitted', 'manager_approved', 'hr_approved', 'approved', 'rejected', 'cancelled'])->default('submitted');
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('manager_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('manager_approved_at')->nullable();
            $table->foreignId('hr_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('hr_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('hr_approved_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
