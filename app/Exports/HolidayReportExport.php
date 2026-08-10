<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class HolidayReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $holidays;

    public function __construct($holidays)
    {
        $this->holidays = $holidays;
    }

    public function collection()
    {
        return $this->holidays;
    }

    public function headings(): array
    {
        return [
            'Employee',
            'Type',
            'Start Date',
            'End Date',
            'Total Days',
            'Status',
            'Reason',
            'Requested At',
        ];
    }

    public function map($holiday): array
    {
        return [
            optional($holiday->employee)->full_name ?? 'Unknown',
            ucwords(str_replace('_', ' ', $holiday->holiday_type)),
            $holiday->start_date->format('d-m-Y'),
            $holiday->end_date->format('d-m-Y'),
            $holiday->total_days,
            ucfirst($holiday->status),
            $holiday->reason ?? '-',
            optional($holiday->requested_at)->format('d-m-Y H:i') ?? '-',
        ];
    }
}
