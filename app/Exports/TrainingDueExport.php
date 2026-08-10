<?php

namespace App\Exports;

use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TrainingDueExport implements FromCollection, WithHeadings, WithMapping
{
    protected $records;

    public function __construct($records)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return $this->records;
    }

    public function headings(): array
    {
        return [
            'Employee',
            'Training Course',
            'Completed On',
            'Expiry Date',
            'Days Until Expiry',
            'Status',
        ];
    }

    public function map($record): array
    {
        $daysUntilExpiry = $record->expiry_date ? Carbon::today()->diffInDays($record->expiry_date, false) : null;

        return [
            optional($record->employee)->full_name ?? 'Unknown',
            optional($record->trainingCourse)->name ?? '-',
            optional($record->completed_at)->format('d-m-Y') ?? '-',
            $record->expiry_date ? $record->expiry_date->format('d-m-Y') : '-',
            $daysUntilExpiry !== null ? $daysUntilExpiry : '-',
            $daysUntilExpiry !== null && $daysUntilExpiry < 0 ? 'Expired' : 'Expiring Soon',
        ];
    }
}
