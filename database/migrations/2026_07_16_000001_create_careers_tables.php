<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::create('career_jobs', function (Blueprint $table) {
      $table->id();
      $table->string('title');
      $table->string('slug')->unique();
      $table->string('department')->index();
      $table->string('location')->index();
      $table->string('employment_type')->index();
      $table->string('workplace_type')->index();
      $table->string('salary_text')->nullable();
      $table->text('summary');
      $table->longText('description');
      $table->longText('responsibilities')->nullable();
      $table->longText('requirements')->nullable();
      $table->longText('preferred_skills')->nullable();
      $table->longText('benefits')->nullable();
      $table->text('equal_opportunity_statement')->nullable();
      $table->string('application_email')->nullable();
      $table->date('closing_date')->nullable()->index();
      $table->string('status')->default('draft')->index();
      $table->unsignedInteger('positions_available')->nullable();
      $table->integer('display_order')->default(0)->index();
      $table->boolean('featured')->default(false)->index();
      $table->timestamp('published_at')->nullable();
      $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
      $table->foreignId('updated_by')->nullable()->constrained('admins')->nullOnDelete();
      $table->timestamps();
      $table->softDeletes();
    });

    Schema::create('career_job_questions', function (Blueprint $table) {
      $table->id();
      $table->foreignId('career_job_id')->constrained('career_jobs')->cascadeOnDelete();
      $table->string('question');
      $table->text('help_text')->nullable();
      $table->string('question_type');
      $table->json('options')->nullable();
      $table->boolean('is_required')->default(false);
      $table->integer('display_order')->default(0);
      $table->timestamps();
    });

    Schema::create('career_applications', function (Blueprint $table) {
      $table->id();
      $table->string('reference_number')->unique();
      $table->foreignId('career_job_id')->nullable()->constrained('career_jobs')->nullOnDelete();
      $table->string('job_title_snapshot')->nullable();
      $table->string('application_type')->default('job')->index();
      $table->string('first_name');
      $table->string('last_name');
      $table->string('email')->index();
      $table->string('phone');
      $table->string('city');
      $table->string('postcode');
      $table->string('right_to_work');
      $table->string('sponsorship_required');
      $table->string('linkedin_url')->nullable();
      $table->string('portfolio_url')->nullable();
      $table->longText('cover_letter_text')->nullable();
      $table->date('earliest_start_date')->nullable();
      $table->string('expected_salary')->nullable();
      $table->text('additional_information')->nullable();
      $table->text('areas_of_interest')->nullable();
      $table->string('preferred_employment_type')->nullable();
      $table->string('cv_path');
      $table->string('cv_original_name');
      $table->string('cv_mime_type');
      $table->unsignedInteger('cv_size');
      $table->string('cover_letter_path')->nullable();
      $table->string('cover_letter_original_name')->nullable();
      $table->string('cover_letter_mime_type')->nullable();
      $table->unsignedInteger('cover_letter_size')->nullable();
      $table->string('status')->default('new')->index();
      $table->string('source')->nullable();
      $table->text('admin_notes')->nullable();
      $table->foreignId('assigned_to')->nullable()->constrained('admins')->nullOnDelete();
      $table->timestamp('submitted_at')->nullable()->index();
      $table->timestamp('reviewed_at')->nullable();
      $table->timestamp('rejected_at')->nullable();
      $table->timestamp('hired_at')->nullable();
      $table->boolean('consent_accepted')->default(false);
      $table->timestamp('consent_accepted_at')->nullable();
      $table->string('privacy_policy_reference')->nullable();
      $table->string('ip_address')->nullable();
      $table->text('user_agent')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });

    Schema::create('career_application_answers', function (Blueprint $table) {
      $table->id();
      $table->foreignId('career_application_id')->constrained('career_applications')->cascadeOnDelete();
      $table->foreignId('career_job_question_id')->nullable()->constrained('career_job_questions')->nullOnDelete();
      $table->string('question_snapshot');
      $table->text('answer_text')->nullable();
      $table->json('answer_json')->nullable();
      $table->timestamps();
    });

    Schema::create('career_application_activities', function (Blueprint $table) {
      $table->id();
      $table->foreignId('career_application_id')->constrained('career_applications')->cascadeOnDelete();
      $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
      $table->string('type')->index();
      $table->string('previous_status')->nullable();
      $table->string('new_status')->nullable();
      $table->text('note')->nullable();
      $table->timestamps();
    });

    Schema::create('career_application_notes', function (Blueprint $table) {
      $table->id();
      $table->foreignId('career_application_id')->constrained('career_applications')->cascadeOnDelete();
      $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
      $table->text('note');
      $table->timestamps();
    });

    Schema::create('career_settings', function (Blueprint $table) {
      $table->id();
      $table->string('key')->unique();
      $table->text('value')->nullable();
      $table->timestamps();
    });

    DB::table('career_settings')->insert([
      ['key' => 'retention_months', 'value' => '12', 'created_at' => now(), 'updated_at' => now()],
      ['key' => 'notification_email', 'value' => null, 'created_at' => now(), 'updated_at' => now()],
    ]);
  }

  public function down()
  {
    Schema::dropIfExists('career_settings');
    Schema::dropIfExists('career_application_notes');
    Schema::dropIfExists('career_application_activities');
    Schema::dropIfExists('career_application_answers');
    Schema::dropIfExists('career_applications');
    Schema::dropIfExists('career_job_questions');
    Schema::dropIfExists('career_jobs');
  }
};


