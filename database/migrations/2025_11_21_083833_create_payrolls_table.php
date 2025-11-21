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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('period'); // YYYY-MM format
            $table->decimal('base_salary', 15, 2);
            $table->integer('attendance_days')->default(0);
            $table->integer('total_work_days')->default(0);
            $table->decimal('late_penalty', 15, 2)->default(0);
            $table->decimal('early_leave_penalty', 15, 2)->default(0);
            $table->decimal('overtime_bonus', 15, 2)->default(0);
            $table->decimal('leave_deduction', 15, 2)->default(0);
            $table->decimal('total_salary', 15, 2);
            $table->timestamp('generated_at')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['user_id', 'period']);
            $table->unique(['user_id', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
