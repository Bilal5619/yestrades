<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AttendanceReportExport implements WithMultipleSheets
{
    protected $officeRows;
    protected $fieldRows;

    public function __construct($officeRows, $fieldRows)
    {
        $this->officeRows = $officeRows;
        $this->fieldRows = $fieldRows;
    }

    public function sheets(): array
    {
        return [
            'Office Staff' => new class($this->officeRows) implements FromArray {
                protected $rows;

                public function __construct($rows)
                {
                    $this->rows = $rows;
                }

                public function array(): array
                {
                    $data = [[
                        'Employee', 'Date', 'Clock In', 'Clock Out', 'Break (min)',
                        'Total Hours', 'Location', 'Status', 'Late', 'Overtime',
                    ]];

                    foreach ($this->rows as $row) {
                        $data[] = [
                            optional($row['employee'])->full_name ?? 'Unknown',
                            $row['shift_date']->format('d-m-Y'),
                            $row['clock_in'] ? $row['clock_in']->format('H:i') : '-',
                            $row['clock_out'] ? $row['clock_out']->format('H:i') : '-',
                            $row['break_minutes'],
                            $row['worked_minutes'] !== null ? round($row['worked_minutes'] / 60, 2) : '-',
                            $row['location'] ?? '-',
                            ucfirst($row['status']),
                            $row['is_late'] ? 'Yes' : 'No',
                            $row['overtime_minutes'] ? round($row['overtime_minutes'] / 60, 2) : '-',
                        ];
                    }

                    return $data;
                }
            },
            'Field Engineers' => new class($this->fieldRows) implements FromArray {
                protected $rows;

                public function __construct($rows)
                {
                    $this->rows = $rows;
                }

                public function array(): array
                {
                    $data = [[
                        'Engineer', 'Job Number', 'Job', 'Customer', 'Started', 'Finished',
                        'Estimated Hours', 'Actual Hours', 'Difference (Hours)', 'Status', 'Late',
                    ]];

                    foreach ($this->rows as $row) {
                        $data[] = [
                            optional($row['employee'])->full_name ?? 'Unknown',
                            $row['job_number'],
                            $row['job']->title,
                            $row['job']->customer_name ?? '-',
                            optional($row['job']->started_at)->format('d-m-Y H:i') ?? '-',
                            optional($row['job']->finished_at)->format('d-m-Y H:i') ?? '-',
                            $row['estimated_minutes'] ? round($row['estimated_minutes'] / 60, 2) : '-',
                            $row['actual_minutes'] !== null ? round($row['actual_minutes'] / 60, 2) : '-',
                            $row['difference_minutes'] !== null ? round($row['difference_minutes'] / 60, 2) : '-',
                            ucwords(str_replace('_', ' ', $row['job']->status)),
                            $row['is_late'] ? 'Yes' : 'No',
                        ];
                    }

                    return $data;
                }
            },
        ];
    }
}
