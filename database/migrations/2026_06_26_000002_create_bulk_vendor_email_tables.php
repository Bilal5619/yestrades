<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::create('bulk_vendor_emails', function (Blueprint $table) {
      $table->id();
      $table->string('subject');
      $table->longText('message');
      $table->enum('send_type', ['all', 'active', 'verified', 'specific']);
      $table->unsignedInteger('total_recipients')->default(0);
      $table->foreignId('sent_by')->nullable()->constrained('admins')->nullOnDelete();
      $table->timestamps();
    });

    Schema::create('bulk_vendor_email_recipients', function (Blueprint $table) {
      $table->id();
      $table->foreignId('bulk_vendor_email_id')->constrained('bulk_vendor_emails')->cascadeOnDelete();
      $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
      $table->string('email');
      $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
      $table->text('error_message')->nullable();
      $table->timestamp('sent_at')->nullable();
      $table->timestamps();

      $table->index(['bulk_vendor_email_id', 'status']);
    });
  }

  public function down()
  {
    Schema::dropIfExists('bulk_vendor_email_recipients');
    Schema::dropIfExists('bulk_vendor_emails');
  }
};
