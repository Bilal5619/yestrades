<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()->after('order_status');
            $table->string('completed_by_type')->nullable()->after('completed_at');
            $table->unsignedBigInteger('completed_by_id')->nullable()->after('completed_by_type');
        });
    }

    public function down()
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->dropColumn(['completed_at', 'completed_by_type', 'completed_by_id']);
        });
    }
};
