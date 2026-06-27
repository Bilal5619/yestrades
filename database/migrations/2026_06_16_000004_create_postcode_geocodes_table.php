<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('postcode_geocodes')) {
            return;
        }

        Schema::create('postcode_geocodes', function (Blueprint $table) {
            $table->id();
            // normalized: uppercase, no spaces (full postcode or outcode)
            $table->string('postcode')->unique();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('postcode_geocodes');
    }
};
