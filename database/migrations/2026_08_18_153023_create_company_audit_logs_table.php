<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('company_audit_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_user_id')
                ->nullable()
                ->constrained('company_users')
                ->nullOnDelete();

            $table->string('action', 255);
            $table->string('method', 10);

            $table->string('route_name', 255)
                ->nullable();

            $table->string('request_path', 500)
                ->nullable();

            $table->string('ip_address', 45)
                ->nullable();

            $table->text('user_agent')
                ->nullable();

            $table->unsignedSmallInteger('response_status')
                ->nullable();

            $table->timestamps();

            $table->index('action');
            $table->index('route_name');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_audit_logs');
    }
};