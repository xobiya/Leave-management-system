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
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->enum('request_unit', ['day', 'half_day', 'hour'])->default('day');
            $table->decimal('requested_hours', 6, 2)->nullable();
            $table->enum('half_day_period', ['am', 'pm'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn(['request_unit', 'requested_hours', 'half_day_period']);
        });
    }
};
