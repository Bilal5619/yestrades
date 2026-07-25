<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuoteQuestionsTables extends Migration
{
    public function up()
    {
        Schema::create('quote_questions', function (Blueprint $table) {
            $table->id();
            // Quote questions are service-only (no package support — packages are
            // out of scope for the Get-a-Quote feature). No hard FK to `services`,
            // matching the existing `questions.service_id` convention.
            $table->unsignedBigInteger('service_id')->index();
            $table->string('title');
            $table->string('summary_label')->nullable();
            $table->boolean('is_required')->default(false);
            $table->string('image')->nullable();
            // short_text|long_text|single_select|multi_select|yes_no|number|quantity|
            // dropdown|date|time|address|postcode|photo_upload|document_upload
            $table->string('type', 50)->default('short_text');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('quote_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_question_id')->constrained('quote_questions')->cascadeOnDelete();
            $table->string('option_text');
            $table->text('description')->nullable();
            $table->json('highlights')->nullable();
            $table->string('option_image')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quote_question_options');
        Schema::dropIfExists('quote_questions');
    }
}
