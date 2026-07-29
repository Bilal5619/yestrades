<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('document_type_id');

            $table->string('title')->nullable();
            $table->string('document_number')->nullable();
            $table->string('file_path')->nullable();
            $table->string('original_file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();

            $table->boolean('is_required')
                ->default(false);

            $table->enum('review_status', [
                'missing',
                'pending_review',
                'approved',
                'rejected',
            ])->default('missing');

            $table->text('review_notes')->nullable();
            $table->text('notes')->nullable();

            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamp('uploaded_at')->nullable();

            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->cascadeOnDelete();

            $table->foreign('document_type_id')
                ->references('id')
                ->on('employee_document_types')
                ->restrictOnDelete();

            $table->foreign('uploaded_by')
                ->references('id')
                ->on('company_users')
                ->nullOnDelete();

            $table->foreign('reviewed_by')
                ->references('id')
                ->on('company_users')
                ->nullOnDelete();

            $table->foreign('created_by')
                ->references('id')
                ->on('company_users')
                ->nullOnDelete();

            $table->foreign('updated_by')
                ->references('id')
                ->on('company_users')
                ->nullOnDelete();

            $table->index('employee_id');
            $table->index('document_type_id');
            $table->index('review_status');
            $table->index('expiry_date');

            $table->unique([
                'employee_id',
                'document_type_id',
            ]);
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_documents');
    }
}