<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class OvertimeReportExport implements WithMultipleSheets
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
            'Office Overtime' => new class($this->officeRows) implements FromArray {
                protected $rows;

                public function __construct($rows)
                {
                    $this->rows = $rows;
                }

                public function array(): array
                {
                    $data = [['Employee', 'Date', 'Clock In', 'Clock Out', 'Scheduled Hours', 'Actual Hours', 'Overtime (Hours)']];

                    foreach ($this->rows as $row) {
                        $data[] = [
                            optional($row['employee'])->full_name ?? 'Unknown',
                            $row['shift_date']->format('d-m-Y'),
                            $row['clock_in'] ? $row['clock_in']->format('H:i') : '-',
                            $row['clock_out'] ? $row['clock_out']->format('H:i') : '-',
                            round(($row['worked_minutes'] - $row['overtime_minutes']) / 60, 2),
                            round($row['worked_minutes'] / 60, 2),
                            round($row['overtime_minutes'] / 60, 2),
                        ];
                    }

                    return $data;
                }
            },
            'Field Overtime' => new class($this->fieldRows) implements FromArray {
                protected $rows;

                public function __construct($rows)
                {
                    $this->rows = $rows;
                }

                public function array(): array
                {
                    $data = [['Engineer', 'Job Number', 'Job', 'Estimated Hours', 'Actual Hours', 'Overtime (Hours)']];

                    foreach ($this->rows as $row) {
                        $data[] = [
                            optional($row['employee'])->full_name ?? 'Unknown',
                            $row['job_number'],
                            $row['job']->title,
                            round($row['estimated_minutes'] / 60, 2),
                            round($row['actual_minutes'] / 60, 2),
                            round($row['difference_minutes'] / 60, 2),
                        ];
                    }

                    return $data;
                }
            },
        ];
    }
}
