<?php

namespace Database\Seeders;

use App\Models\Company\EmployeeDocumentType;
use Illuminate\Database\Seeder;

class EmployeeDocumentTypeSeeder extends Seeder
{
    public function run()
    {
        $documentTypes = [
            [
                'name' => 'Passport',
                'slug' => 'passport',
                'category' => 'identity',
                'description' => 'Employee passport or identity evidence.',
                'is_required_by_default' => true,
                'requires_expiry_date' => true,
                'requires_review' => true,
                'status' => true,
            ],
            [
                'name' => 'Right to Work Evidence',
                'slug' => 'right-to-work-evidence',
                'category' => 'right_to_work',
                'description' => 'Evidence of the employee’s UK right to work.',
                'is_required_by_default' => true,
                'requires_expiry_date' => true,
                'requires_review' => true,
                'status' => true,
            ],
            [
                'name' => 'Employment Contract',
                'slug' => 'employment-contract',
                'category' => 'employment',
                'description' => 'Signed employment contract.',
                'is_required_by_default' => true,
                'requires_expiry_date' => false,
                'requires_review' => true,
                'status' => true,
            ],
            [
                'name' => 'Proof of Address',
                'slug' => 'proof-of-address',
                'category' => 'address',
                'description' => 'Recent evidence of the employee’s home address.',
                'is_required_by_default' => false,
                'requires_expiry_date' => false,
                'requires_review' => true,
                'status' => true,
            ],
            [
                'name' => 'Driving Licence',
                'slug' => 'driving-licence',
                'category' => 'identity',
                'description' => 'Driving licence where the employee drives for work.',
                'is_required_by_default' => false,
                'requires_expiry_date' => true,
                'requires_review' => true,
                'status' => true,
            ],
            [
                'name' => 'DBS Certificate',
                'slug' => 'dbs-certificate',
                'category' => 'qualification',
                'description' => 'Disclosure and Barring Service certificate.',
                'is_required_by_default' => false,
                'requires_expiry_date' => false,
                'requires_review' => true,
                'status' => true,
            ],
            [
                'name' => 'Trade Certificate',
                'slug' => 'trade-certificate',
                'category' => 'qualification',
                'description' => 'Gas, electrical, plumbing or other trade certificate.',
                'is_required_by_default' => false,
                'requires_expiry_date' => true,
                'requires_review' => true,
                'status' => true,
            ],
            [
                'name' => 'Training Certificate',
                'slug' => 'training-certificate',
                'category' => 'training',
                'description' => 'Employee training or course certificate.',
                'is_required_by_default' => false,
                'requires_expiry_date' => true,
                'requires_review' => true,
                'status' => true,
            ],
            [
                'name' => 'Other Document',
                'slug' => 'other-document',
                'category' => 'other',
                'description' => 'Other employee-related document.',
                'is_required_by_default' => false,
                'requires_expiry_date' => false,
                'requires_review' => true,
                'status' => true,
            ],
          [
    'name' => 'Immigration Status Evidence',
    'slug' => 'immigration-status-evidence',
    'category' => 'right_to_work',
    'description' => 'Evidence of the sponsored worker’s current immigration status or eVisa.',
    'is_required_by_default' => false,
    'requires_expiry_date' => true,
    'requires_review' => true,
    'status' => true,
],
[
    'name' => 'Certificate of Sponsorship Evidence',
    'slug' => 'certificate-of-sponsorship-evidence',
    'category' => 'employment',
    'description' => 'Certificate of Sponsorship details, allocation evidence or related record.',
    'is_required_by_default' => false,
    'requires_expiry_date' => false,
    'requires_review' => true,
    'status' => true,
],
[
    'name' => 'Sponsored Job Description',
    'slug' => 'sponsored-job-description',
    'category' => 'employment',
    'description' => 'Job description covering the sponsored role and duties.',
    'is_required_by_default' => false,
    'requires_expiry_date' => false,
    'requires_review' => true,
    'status' => true,
],
[
    'name' => 'Recruitment Evidence',
    'slug' => 'recruitment-evidence',
    'category' => 'employment',
    'description' => 'Recruitment records and evidence retained for the sponsored role.',
    'is_required_by_default' => false,
    'requires_expiry_date' => false,
    'requires_review' => true,
    'status' => true,
],
[
    'name' => 'Qualification Evidence',
    'slug' => 'qualification-evidence',
    'category' => 'qualification',
    'description' => 'Qualifications or professional evidence supporting the sponsored role.',
    'is_required_by_default' => false,
    'requires_expiry_date' => false,
    'requires_review' => true,
    'status' => true,
],
[
    'name' => 'Salary and Payment Evidence',
    'slug' => 'salary-payment-evidence',
    'category' => 'employment',
    'description' => 'Payslips, payroll records or bank-payment evidence for the sponsored worker.',
    'is_required_by_default' => false,
    'requires_expiry_date' => false,
    'requires_review' => true,
    'status' => true,
],
        ];

        foreach ($documentTypes as $documentType) {
            EmployeeDocumentType::updateOrCreate(
                [
                    'slug' => $documentType['slug'],
                ],
                $documentType
            );
        }
    }
}