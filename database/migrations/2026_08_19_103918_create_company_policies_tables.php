<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_policies', function (Blueprint $table) {
            $table->id();

            $table->string('title');

            $table->text('description')->nullable();

            $table->string('version')
                ->nullable();

            $table->string('file_path')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('company_users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('company_users')
                ->nullOnDelete();

            $table->timestamps();
        });

        Schema::create(
            'company_policy_acknowledgements',
            function (Blueprint $table) {

                $table->id();

                $table->foreignId('company_policy_id')
                    ->constrained('company_policies')
                    ->cascadeOnDelete();

                $table->foreignId('employee_id')
                    ->constrained('employees')
                    ->cascadeOnDelete();

                $table->string('status')
                    ->default('pending');

                $table->timestamp('acknowledged_at')
                    ->nullable();

                $table->timestamps();

                $table->unique([
                    'company_policy_id',
                    'employee_id',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'company_policy_acknowledgements'
        );

        Schema::dropIfExists(
            'company_policies'
        );
    }
};