<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuoteRequestAnswersTable extends Migration
{
    public function up()
    {
        Schema::create('quote_request_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_request_id')->constrained('quote_requests')->cascadeOnDelete();
            $table->unsignedBigInteger('quote_question_id')->index();
            // Pre-resolved to display text (select/multi-select option ids are
            // resolved to option_text before saving) — mirrors BookingAnswer.
            $table->longText('answer')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quote_request_answers');
    }
}
