<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicePackagesAndRenewalFields extends Migration
{
    public function up()
    {
        Schema::create('service_packages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('offer_price', 10, 2)->nullable();
            $table->string('offer_label')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('show_in_renewal_section')->default(false);
            $table->integer('renewal_section_sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('service_package_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_package_id')->constrained('service_packages')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['service_package_id', 'service_id']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->boolean('show_in_renewal_section')->default(false)->after('price_placeholder');
            $table->integer('renewal_section_sort_order')->default(0)->after('show_in_renewal_section');
            $table->decimal('offer_price', 10, 2)->nullable()->after('renewal_section_sort_order');
            $table->string('offer_label')->nullable()->after('offer_price');
            $table->text('renewal_short_description')->nullable()->after('offer_label');
        });

        Schema::table('service_bookings', function (Blueprint $table) {
            $table->string('item_type')->default('service')->after('group_token');
            $table->unsignedBigInteger('service_package_id')->nullable()->after('service_id');
            $table->string('package_name')->nullable()->after('service_package_id');
            $table->decimal('package_price', 10, 2)->nullable()->after('package_name');
            $table->json('package_included_services')->nullable()->after('package_price');
        });
    }

    public function down()
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->dropColumn([
                'item_type',
                'service_package_id',
                'package_name',
                'package_price',
                'package_included_services',
            ]);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'show_in_renewal_section',
                'renewal_section_sort_order',
                'offer_price',
                'offer_label',
                'renewal_short_description',
            ]);
        });

        Schema::dropIfExists('service_package_items');
        Schema::dropIfExists('service_packages');
    }
}
