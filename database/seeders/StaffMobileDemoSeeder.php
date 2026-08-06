<?php

namespace Database\Seeders;

use App\Models\Company\Announcement;
use App\Models\Company\ContractTemplate;
use App\Models\Company\Employee;
use App\Models\Company\EmployeeContract;
use App\Models\Company\EmployeeHolidayAllowance;
use App\Models\Company\EmployeeOnboarding;
use App\Models\Company\EmployeePayslip;
use App\Models\Company\TrainingCourse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StaffMobileDemoSeeder extends Seeder
{
  public function run(): void
  {
    $employee = Employee::updateOrCreate(
      ['employee_number' => 'YT-DEMO-001'],
      [
        'first_name' => 'Demo',
        'last_name' => 'Employee',
        'personal_email' => 'demo.staff@yestrades.test',
        'work_email' => 'demo.staff@yestrades.test',
        'phone' => '07123 456789',
        'address_line_1' => '12 Trade Street',
        'city' => 'London',
        'postcode' => 'SW1A 1AA',
        'country' => 'United Kingdom',
        'national_insurance_number' => 'QQ123456C',
        'job_title' => 'Maintenance Operative',
        'department' => 'Operations',
        'employment_type' => 'full_time',
        'employment_status' => 'active',
        'work_location' => 'London Service Area',
        'normal_weekly_hours' => 40,
        'start_date' => now()->subYear()->toDateString(),
        'emergency_contact_name' => 'Sam Contact',
        'emergency_contact_relationship' => 'Sibling',
        'emergency_contact_phone' => '07999 000111',
        'password' => Hash::make('Password123!'),
        'activated_at' => now(),
      ]
    );

    EmployeeHolidayAllowance::updateOrCreate(
      ['employee_id' => $employee->id, 'holiday_year' => now()->year],
      ['annual_entitlement' => 28, 'carried_over' => 2, 'adjustment' => 0]
    );

    $employee->holidays()->updateOrCreate(
      ['start_date' => now()->addDays(14)->toDateString(), 'end_date' => now()->addDays(15)->toDateString()],
      ['holiday_type' => 'annual_leave', 'total_days' => 2, 'status' => 'pending', 'reason' => 'Family appointment', 'requested_at' => now()]
    );

    $employee->holidays()->updateOrCreate(
      ['start_date' => now()->subMonths(2)->toDateString(), 'end_date' => now()->subMonths(2)->addDay()->toDateString()],
      ['holiday_type' => 'annual_leave', 'total_days' => 2, 'status' => 'approved', 'approved_at' => now()->subMonths(3)]
    );

    $course = TrainingCourse::updateOrCreate(
      ['slug' => 'demo-health-safety'],
      [
        'name' => 'Health & Safety Essentials',
        'category' => 'Compliance',
        'description' => 'Core site safety training for field staff.',
        'is_mandatory' => true,
        'requires_certificate' => true,
        'has_expiry' => true,
        'validity_months' => 12,
        'is_active' => true,
      ]
    );

    $employee->trainingRecords()->updateOrCreate(
      ['training_course_id' => $course->id],
      [
        'provider_name' => 'Yes Trades HR',
        'scheduled_date' => now()->addDays(7)->toDateString(),
        'expiry_date' => now()->addMonths(11)->toDateString(),
        'status' => 'scheduled',
        'certificate_number' => null,
      ]
    );

    $this->seedPrivatePdf('company-private/payslips/'.$employee->id.'/demo-july-2026.pdf', 'Demo payslip');
    EmployeePayslip::updateOrCreate(
      ['employee_id' => $employee->id, 'pay_period' => 'July 2026'],
      [
        'payment_date' => now()->startOfMonth()->toDateString(),
        'original_filename' => 'yes-trades-payslip-july-2026.pdf',
        'stored_filename' => 'demo-july-2026.pdf',
        'file_path' => 'company-private/payslips/'.$employee->id.'/demo-july-2026.pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 128,
      ]
    );

    $onboarding = EmployeeOnboarding::firstOrCreate(
      ['employee_id' => $employee->id],
      ['status' => 'completed', 'progress_percentage' => 100, 'started_at' => now()]
    );

    $template = ContractTemplate::firstOrCreate(
      ['name' => 'Demo Employment Contract'],
      [
        'employment_type' => 'full_time',
        'description' => 'Demo staff employment contract.',
        'content' => '<p>Demo contract.</p>',
        'is_active' => true,
      ]
    );

    $contractPath = 'company-private/contracts/'.$employee->id.'/generated/demo-contract.pdf';
    $this->seedPrivatePdf($contractPath, 'Demo contract');

    EmployeeContract::updateOrCreate(
      ['employee_id' => $employee->id, 'version' => 1],
      [
        'employee_onboarding_id' => $onboarding->id,
        'contract_template_id' => $template->id,
        'status' => 'generated',
        'generated_content' => '<p>Demo contract.</p>',
        'generated_file_path' => $contractPath,
        'generated_filename' => 'yes-trades-employment-contract.pdf',
        'generated_at' => now()->subMonth(),
      ]
    );

    foreach (range(0, 4) as $offset) {
      $date = now()->startOfWeek()->addDays($offset);
      $employee->shifts()->updateOrCreate(
        ['shift_date' => $date->toDateString(), 'start_time' => '08:00'],
        ['end_time' => '16:30', 'break_minutes' => 30, 'work_location' => 'London Service Area', 'status' => 'scheduled']
      );
    }

    $employee->jobs()->updateOrCreate(
      ['scheduled_date' => now()->toDateString(), 'scheduled_start_time' => '10:00'],
      [
        'title' => 'Boiler inspection',
        'description' => 'Inspect reported pressure issue and record findings.',
        'customer_name' => 'A. Customer',
        'customer_phone' => '020 7000 0000',
        'site_address' => '21 Customer Road, London',
        'site_postcode' => 'E1 6AN',
        'expected_duration_minutes' => 90,
        'priority' => 'high',
        'status' => 'assigned',
        'internal_notes' => 'Take photos before and after.',
      ]
    );

    $employee->jobs()->updateOrCreate(
      ['scheduled_date' => now()->addDay()->toDateString(), 'scheduled_start_time' => '09:30'],
      [
        'title' => 'Bathroom repair follow-up',
        'description' => 'Return visit to confirm sealant and pipework.',
        'customer_name' => 'B. Customer',
        'customer_phone' => '020 7111 2222',
        'site_address' => '8 Market Lane, London',
        'site_postcode' => 'N1 9GU',
        'expected_duration_minutes' => 120,
        'priority' => 'medium',
        'status' => 'accepted',
        'internal_notes' => 'Customer requested morning arrival.',
      ]
    );

    Announcement::updateOrCreate(
      ['title' => 'August rota reminder'],
      [
        'body' => 'Please check your mobile rota each evening for next-day jobs and shifts.',
        'is_urgent' => false,
        'published_at' => now()->subDay(),
      ]
    );
  }

  private function seedPrivatePdf(string $path, string $title): void
  {
    if (Storage::disk('local')->exists($path)) {
      return;
    }

    $content = "%PDF-1.4\n1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj\n2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj\n3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 300 144] /Contents 4 0 R >> endobj\n4 0 obj << /Length 64 >> stream\nBT /F1 18 Tf 40 90 Td (".$title." - Yes Trades) Tj ET\nendstream endobj\nxref\n0 5\n0000000000 65535 f \ntrailer << /Root 1 0 R /Size 5 >>\nstartxref\n320\n%%EOF";

    Storage::disk('local')->put($path, $content);
  }
}
