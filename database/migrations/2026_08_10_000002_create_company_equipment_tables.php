<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['vehicle', 'tool', 'device', 'other'])->default('other');
            $table->string('reference_number')->nullable();
            $table->string('registration_number')->nullable();
            $table->enum('status', ['available', 'assigned', 'maintenance', 'retired'])->default('available');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('company_users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('company_users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('company_equipment_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('company_equipment')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->timestamp('assigned_at');
            $table->timestamp('returned_at')->nullable();
            $table->string('condition_on_return')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('company_users')->nullOnDelete();
            $table->foreignId('returned_to')->nullable()->constrained('company_users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_equipment_assignments');
        Schema::dropIfExists('company_equipment');
    }
};
