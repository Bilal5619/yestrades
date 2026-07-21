<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Careers\CareerApplication;
use App\Models\Careers\CareerJob;
use App\Models\Careers\CareerJobQuestion;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CareersModuleTest extends TestCase
{
  use DatabaseTransactions;

  public function test_only_published_active_jobs_are_in_active_scope(): void
  {
    $published = CareerJob::create($this->jobData(['title' => 'Engineer', 'slug' => 'engineer', 'status' => 'published']));
    CareerJob::create($this->jobData(['title' => 'Draft', 'slug' => 'draft', 'status' => 'draft']));
    CareerJob::create($this->jobData(['title' => 'Closed', 'slug' => 'closed', 'status' => 'closed']));
    CareerJob::create($this->jobData(['title' => 'Expired', 'slug' => 'expired', 'status' => 'published', 'closing_date' => now()->subDay()->toDateString()]));

    $this->assertTrue(CareerJob::active()->pluck('id')->contains($published->id));
    $this->assertSame(1, CareerJob::active()->count());
  }

  public function test_job_application_can_be_submitted_with_private_documents(): void
  {
    Mail::fake();
    Storage::fake('local');

    $job = CareerJob::create($this->jobData(['status' => 'published', 'slug' => 'gas-engineer']));
    $question = CareerJobQuestion::create(['career_job_id' => $job->id, 'question' => 'Do you have Gas Safe registration?', 'question_type' => 'yes_no', 'is_required' => true]);

    $response = $this->post(route('careers.store', $job), $this->applicationData([
      'questions' => [$question->id => 'Yes'],
      'cv' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
    ]));

    $application = CareerApplication::first();
    $response->assertRedirect(route('careers.success', $application->reference_number));
    $this->assertNotNull($application->reference_number);
    $this->assertNotEquals('cv.pdf', basename($application->cv_path));
    Storage::disk('local')->assertExists($application->cv_path);
    $this->assertDatabaseHas('career_application_answers', ['question_snapshot' => 'Do you have Gas Safe registration?', 'answer_text' => 'Yes']);
  }

  public function test_general_cv_can_be_submitted(): void
  {
    Mail::fake();
    Storage::fake('local');

    $response = $this->post(route('careers.general.store'), $this->applicationData([
      'areas_of_interest' => 'Operations and customer support',
      'cv' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
    ]));

    $application = CareerApplication::first();
    $response->assertRedirect(route('careers.success', $application->reference_number));
    $this->assertSame('general', $application->application_type);
  }

  public function test_invalid_file_and_missing_consent_are_rejected(): void
  {
    $job = CareerJob::create($this->jobData(['status' => 'published']));

    $response = $this->from(route('careers.show', $job->slug))->post(route('careers.store', $job), $this->applicationData([
      'privacy_consent' => null,
      'cv' => UploadedFile::fake()->create('cv.exe', 10, 'application/x-msdownload'),
    ]));

    $response->assertSessionHasErrors(['privacy_consent', 'cv']);
    $this->assertSame(0, CareerApplication::count());
  }

  public function test_unauthorised_users_cannot_download_application_files(): void
  {
    $application = CareerApplication::create($this->applicationRecord(['cv_path' => 'career-documents/test.pdf']));

    $this->get(route('admin.careers.applications.download-cv', $application))->assertRedirect(route('admin.login'));
  }

  public function test_admin_can_download_files_and_status_changes_are_logged(): void
  {
    Storage::fake('local');
    Storage::disk('local')->put('career-documents/test.pdf', 'pdf');
    $admin = Admin::create(['first_name' => 'Test', 'last_name' => 'Admin', 'username' => 'careeradmin', 'email' => 'careeradmin@example.test', 'password' => bcrypt('password'), 'status' => 1]);
    $application = CareerApplication::create($this->applicationRecord(['cv_path' => 'career-documents/test.pdf']));

    $this->actingAs($admin, 'admin')->get(route('admin.careers.applications.download-cv', $application))->assertOk();
    $this->actingAs($admin, 'admin')->post(route('admin.careers.applications.update-status', $application), ['status' => 'reviewing', 'note' => 'Review started'])->assertRedirect();

    $this->assertDatabaseHas('career_application_activities', ['career_application_id' => $application->id, 'type' => 'cv_downloaded']);
    $this->assertDatabaseHas('career_application_activities', ['career_application_id' => $application->id, 'type' => 'status_changed', 'previous_status' => 'new', 'new_status' => 'reviewing']);
  }

  private function jobData(array $overrides = []): array
  {
    return array_merge([
      'title' => 'Service Engineer',
      'slug' => 'service-engineer-' . uniqid(),
      'department' => 'Operations',
      'location' => 'London',
      'employment_type' => 'Full-time',
      'workplace_type' => 'Field-based',
      'summary' => 'Support customers across the UK.',
      'description' => '<p>Install and maintain services.</p>',
      'status' => 'draft',
      'display_order' => 0,
    ], $overrides);
  }

  private function applicationData(array $overrides = []): array
  {
    return array_merge([
      'first_name' => 'Alex',
      'last_name' => 'Taylor',
      'email' => 'alex@example.test',
      'phone' => '07123456789',
      'city' => 'London',
      'postcode' => 'SW1A 1AA',
      'right_to_work' => 'Yes',
      'sponsorship_required' => 'No',
      'privacy_consent' => '1',
      'cv' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
    ], $overrides);
  }

  private function applicationRecord(array $overrides = []): array
  {
    return array_merge([
      'reference_number' => CareerApplication::makeReferenceNumber(),
      'application_type' => 'general',
      'first_name' => 'Alex',
      'last_name' => 'Taylor',
      'email' => 'alex@example.test',
      'phone' => '07123456789',
      'city' => 'London',
      'postcode' => 'SW1A 1AA',
      'right_to_work' => 'Yes',
      'sponsorship_required' => 'No',
      'cv_path' => 'career-documents/test.pdf',
      'cv_original_name' => 'cv.pdf',
      'cv_mime_type' => 'application/pdf',
      'cv_size' => 100,
      'status' => 'new',
      'submitted_at' => now(),
      'consent_accepted' => true,
      'consent_accepted_at' => now(),
    ], $overrides);
  }
}
