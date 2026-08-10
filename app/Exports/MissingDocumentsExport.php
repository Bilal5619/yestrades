<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MissingDocumentsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $employees;

    public function __construct($employees)
    {
        $this->employees = $employees;
    }

    public function collection()
    {
        return $this->employees;
    }

    public function headings(): array
    {
        return [
            'Employee Number',
            'Employee Name',
            'Department',
            'Job Title',
            'Missing Documents',
        ];
    }

    public function map($employee): array
    {
        $missing = $employee->documents
            ->map(fn ($document) => optional($document->documentType)->name ?? 'Unnamed document')
            ->implode(', ');

        return [
            $employee->employee_number,
            $employee->full_name,
            $employee->department ?? '-',
            $employee->job_title ?? '-',
            $missing ?: '-',
        ];
    }
}
