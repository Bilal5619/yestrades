<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create(
            'training_courses',
            function (Blueprint $table) {
                $table->id();

                $table->string('name', 180);
                $table->string('slug', 200)->unique();

                $table->string('category', 120)
                    ->nullable();

                $table->text('description')
                    ->nullable();

                $table->boolean('is_mandatory')
                    ->default(false);

                $table->boolean('requires_certificate')
                    ->default(false);

                $table->boolean('has_expiry')
                    ->default(false);

                $table->unsignedInteger('validity_months')
                    ->nullable();

                $table->boolean('is_active')
                    ->default(true);

                $table->unsignedBigInteger('created_by')
                    ->nullable();

                $table->unsignedBigInteger('updated_by')
                    ->nullable();

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
                $table->index('is_mandatory');
                $table->index('is_active');
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists('training_courses');
    }
};