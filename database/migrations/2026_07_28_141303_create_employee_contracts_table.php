<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create(
            'employee_contracts',
            function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger(
                    'employee_onboarding_id'
                );

                $table->foreign(
                    'employee_onboarding_id',
                    'employee_contract_onboarding_fk'
                )
                    ->references('id')
                    ->on('employee_onboardings')
                    ->cascadeOnDelete();

                $table->unsignedBigInteger('employee_id');

                $table->foreign(
                    'employee_id',
                    'employee_contract_employee_fk'
                )
                    ->references('id')
                    ->on('employees')
                    ->cascadeOnDelete();

                $table->unsignedBigInteger(
                    'contract_template_id'
                );

                $table->foreign(
                    'contract_template_id',
                    'employee_contract_template_fk'
                )
                    ->references('id')
                    ->on('contract_templates')
                    ->restrictOnDelete();

                $table->unsignedInteger('version')
                    ->default(1);

                $table->string('status', 30)
    ->default('draft');

                $table->longText('generated_content')
                    ->nullable();

                $table->string('generated_file_path')
                    ->nullable();

                $table->string('generated_filename')
                    ->nullable();

                $table->string('signed_file_path')
                    ->nullable();

                $table->string('signed_filename')
                    ->nullable();

                $table->timestamp('generated_at')
                    ->nullable();

                $table->timestamp('sent_for_signature_at')
                    ->nullable();

                $table->timestamp('signed_at')
                    ->nullable();

                $table->unsignedBigInteger('generated_by')
                    ->nullable();

                $table->foreign(
                    'generated_by',
                    'employee_contract_generated_by_fk'
                )
                    ->references('id')
                    ->on('company_users')
                    ->nullOnDelete();

                $table->unsignedBigInteger('signed_uploaded_by')
                    ->nullable();

                $table->foreign(
                    'signed_uploaded_by',
                    'employee_contract_signed_by_fk'
                )
                    ->references('id')
                    ->on('company_users')
                    ->nullOnDelete();

                $table->text('notes')
                    ->nullable();

                $table->timestamps();

                $table->index(
    [
        'employee_id',
        'status',
    ],
    'employee_contract_emp_status_idx'
);
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists('employee_contracts');
    }
};