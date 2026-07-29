<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employee_payslips', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('employee_id');

            $table->foreign(
                'employee_id',
                'employee_payslip_employee_fk'
            )
                ->references('id')
                ->on('employees')
                ->cascadeOnDelete();

            $table->string('pay_period');
            $table->date('payment_date');

            $table->string('original_filename');
            $table->string('stored_filename');
            $table->string('file_path');

            $table->string('mime_type')
                ->nullable();

            $table->unsignedBigInteger('file_size')
                ->nullable();

            $table->text('notes')
                ->nullable();

            $table->unsignedBigInteger('uploaded_by')
                ->nullable();

            $table->foreign(
                'uploaded_by',
                'employee_payslip_uploaded_by_fk'
            )
                ->references('id')
                ->on('company_users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index([
                'employee_id',
                'payment_date',
            ]);
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_payslips');
    }
};