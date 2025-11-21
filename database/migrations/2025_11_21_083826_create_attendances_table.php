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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            
            // Check-in data
            $table->time('check_in')->nullable();
            $table->decimal('check_in_latitude', 10, 8)->nullable();
            $table->decimal('check_in_longitude', 11, 8)->nullable();
            $table->string('check_in_photo')->nullable();
            $table->decimal('check_in_face_score', 5, 2)->nullable();
            $table->string('check_in_address')->nullable();
            
            // Check-out data
            $table->time('check_out')->nullable();
            $table->decimal('check_out_latitude', 10, 8)->nullable();
            $table->decimal('check_out_longitude', 11, 8)->nullable();
            $table->string('check_out_photo')->nullable();
            $table->decimal('check_out_face_score', 5, 2)->nullable();
            $table->string('check_out_address')->nullable();
            
            // Validation data
            $table->string('qr_token')->nullable();
            $table->enum('status', ['valid', 'invalid', 'pending'])->default('pending');
            $table->text('notes')->nullable();
            
            // Working hours
            $table->integer('work_duration')->nullable(); // in minutes
            $table->integer('late_duration')->nullable(); // in minutes
            $table->integer('early_leave_duration')->nullable(); // in minutes
            
            $table->timestamps();
            
            $table->index(['user_id', 'date']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
