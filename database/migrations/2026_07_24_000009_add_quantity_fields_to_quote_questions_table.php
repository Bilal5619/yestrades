<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantityFieldsToQuoteQuestionsTable extends Migration
{
    public function up()
    {
        Schema::table('quote_questions', function (Blueprint $table) {
            // Supports the 'quantity' question type (e.g. "how many rooms?").
            // No quantity_unit_price — quote questions never affect price.
            $table->unsignedInteger('quantity_min')->nullable()->after('type');
            $table->unsignedInteger('quantity_max')->nullable()->after('quantity_min');
            $table->unsignedInteger('quantity_default')->nullable()->after('quantity_max');
        });
    }

    public function down()
    {
        Schema::table('quote_questions', function (Blueprint $table) {
            $table->dropColumn(['quantity_min', 'quantity_max', 'quantity_default']);
        });
    }
}
