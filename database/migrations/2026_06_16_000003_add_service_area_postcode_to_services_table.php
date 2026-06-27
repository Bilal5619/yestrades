<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('services')) {
            return;
        }

        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'service_area_postcode')) {
                $table->string('service_area_postcode')->nullable();
            }
            // latitude/longitude already exist on the table; ensure they are present
            // for installs that lack them so backend geocoding has somewhere to write.
            if (!Schema::hasColumn('services', 'latitude')) {
                $table->string('latitude')->nullable();
            }
            if (!Schema::hasColumn('services', 'longitude')) {
                $table->string('longitude')->nullable();
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('services')) {
            return;
        }

        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'service_area_postcode')) {
                $table->dropColumn('service_area_postcode');
            }
        });
    }
};
