<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create(
            'sponsored_worker_changes',
            function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger(
                    'sponsored_worker_record_id'
                );

                $table->string('change_type', 60);
                $table->string('field_name', 100)->nullable();

                $table->text('old_value')->nullable();
                $table->text('new_value')->nullable();

                $table->date('effective_date')->nullable();
                $table->dateTime('detected_at')->nullable();

                $table->boolean('requires_ukvi_review')
                    ->default(true);

                $table->string('review_status', 30)
                    ->default('pending');

                $table->text('notes')->nullable();

                $table->unsignedBigInteger(
                    'created_by'
                )->nullable();

                $table->unsignedBigInteger(
                    'reviewed_by'
                )->nullable();

                $table->dateTime('reviewed_at')->nullable();

                $table->timestamps();

                $table->foreign(
                    'sponsored_worker_record_id',
                    'swc_record_fk'
                )
                    ->references('id')
                    ->on('sponsored_worker_records')
                    ->cascadeOnDelete();

                $table->foreign(
                    'created_by',
                    'swc_created_by_fk'
                )
                    ->references('id')
                    ->on('company_users')
                    ->nullOnDelete();

                $table->foreign(
                    'reviewed_by',
                    'swc_reviewed_by_fk'
                )
                    ->references('id')
                    ->on('company_users')
                    ->nullOnDelete();

                $table->index(
                    [
                        'sponsored_worker_record_id',
                        'review_status',
                    ],
                    'swc_record_status_idx'
                );

                $table->index(
                    'effective_date',
                    'swc_effective_date_idx'
                );
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists(
            'sponsored_worker_changes'
        );
    }
};