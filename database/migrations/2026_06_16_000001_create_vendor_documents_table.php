<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('vendor_documents')) {
            return;
        }

        Schema::create('vendor_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id')->index();
            // insurance | id_document | right_to_work
            $table->string('type');
            $table->string('file_path');
            // for id_document: driving_licence | passport
            $table->string('id_type')->nullable();
            // for insurance
            $table->string('policy_number')->nullable();
            $table->date('expiry_date')->nullable();
            // for right_to_work
            $table->date('date_of_birth')->nullable();
            // pending | under_review | approved | rejected
            $table->string('status')->default('under_review');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->unique(['vendor_id', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendor_documents');
    }
};
