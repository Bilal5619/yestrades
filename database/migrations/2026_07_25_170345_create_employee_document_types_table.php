<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeDocumentTypesTable extends Migration
{
    public function up()
    {
        Schema::create('employee_document_types', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();

            $table->enum('category', [
                'identity',
                'right_to_work',
                'employment',
                'qualification',
                'training',
                'address',
                'other',
            ])->default('other');

            $table->text('description')->nullable();

            $table->boolean('is_required_by_default')
                ->default(false);

            $table->boolean('requires_expiry_date')
                ->default(false);

            $table->boolean('requires_review')
                ->default(true);

            $table->boolean('status')
                ->default(true);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            $table->foreign('created_by')
                ->references('id')
                ->on('company_users')
                ->nullOnDelete();

            $table->foreign('updated_by')
                ->references('id')
                ->on('company_users')
                ->nullOnDelete();

            $table->index('category');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_document_types');
    }
}