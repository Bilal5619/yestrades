<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create(
            'contract_templates',
            function (Blueprint $table) {
                $table->id();

                $table->string('name');

               $table->string('employment_type', 50)
                    ->nullable();

                $table->string('description')
                    ->nullable();

                $table->longText('content');

                $table->boolean('is_default')
                    ->default(false);

                $table->boolean('is_active')
                    ->default(true);

                $table->unsignedInteger('version')
                    ->default(1);

                $table->unsignedBigInteger('created_by')
                    ->nullable();

                $table->foreign(
                    'created_by',
                    'contract_templates_created_by_fk'
                )
                    ->references('id')
                    ->on('company_users')
                    ->nullOnDelete();

                $table->unsignedBigInteger('updated_by')
                    ->nullable();

                $table->foreign(
                    'updated_by',
                    'contract_templates_updated_by_fk'
                )
                    ->references('id')
                    ->on('company_users')
                    ->nullOnDelete();

                $table->timestamps();

                $table->index(
                    [
                        'employment_type',
                        'is_active',
                    ],
                    'contract_templates_type_active_idx'
                );
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists('contract_templates');
    }
};