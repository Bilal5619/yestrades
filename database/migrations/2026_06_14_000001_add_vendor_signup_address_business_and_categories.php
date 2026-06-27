<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    if (Schema::hasTable('vendors') && !Schema::hasColumn('vendors', 'business_type')) {
      Schema::table('vendors', function (Blueprint $table) {
        $column = $table->string('business_type')->nullable();

        if (Schema::hasColumn('vendors', 'fixed_commission_percent')) {
          $column->after('fixed_commission_percent');
        }
      });
    }

    if (Schema::hasTable('vendor_infos') && !Schema::hasColumn('vendor_infos', 'postcode')) {
      Schema::table('vendor_infos', function (Blueprint $table) {
        $column = $table->string('postcode')->nullable();

        if (Schema::hasColumn('vendor_infos', 'zip_code')) {
          $column->after('zip_code');
        }
      });
    }

    if (
      Schema::hasTable('vendors') &&
      Schema::hasTable('service_categories') &&
      !Schema::hasTable('vendor_service_categories')
    ) {
      Schema::create('vendor_service_categories', function (Blueprint $table) {
        $table->id();
        $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
        $table->foreignId('service_category_id')->constrained('service_categories')->cascadeOnDelete();
        $table->timestamps();

        $table->unique(['vendor_id', 'service_category_id'], 'vendor_service_categories_unique');
      });
    }
  }

  public function down()
  {
    Schema::dropIfExists('vendor_service_categories');

    if (Schema::hasTable('vendor_infos') && Schema::hasColumn('vendor_infos', 'postcode')) {
      Schema::table('vendor_infos', function (Blueprint $table) {
        $table->dropColumn('postcode');
      });
    }

    if (Schema::hasTable('vendors') && Schema::hasColumn('vendors', 'business_type')) {
      Schema::table('vendors', function (Blueprint $table) {
        $table->dropColumn('business_type');
      });
    }
  }
};
