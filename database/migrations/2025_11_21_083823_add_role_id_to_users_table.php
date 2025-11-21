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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->onDelete('set null');
            $table->string('phone')->nullable()->after('email');
            $table->string('employee_id')->unique()->nullable()->after('phone');
            $table->decimal('base_salary', 15, 2)->default(0)->after('employee_id');
            $table->string('position')->nullable()->after('base_salary');
            $table->foreignId('supervisor_id')->nullable()->after('position')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id', 'phone', 'employee_id', 'base_salary', 'position', 'supervisor_id']);
        });
    }
};
