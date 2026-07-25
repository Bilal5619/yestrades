<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuoteRequestAttachmentsTable extends Migration
{
    public function up()
    {
        Schema::create('quote_request_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_request_id')->constrained('quote_requests')->cascadeOnDelete();
            // Null for the general end-of-form uploader; set when the file came
            // from a specific photo_upload/document_upload quote question.
            $table->unsignedBigInteger('quote_question_id')->nullable();
            $table->string('file_name');
            $table->string('original_name')->nullable();
            $table->string('file_type', 20); // 'image' | 'document'
            $table->string('uploaded_by_type', 20); // 'customer' | 'admin'
            $table->unsignedBigInteger('uploaded_by_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quote_request_attachments');
    }
}
