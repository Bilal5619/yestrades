<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExpenseReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $expenses;

    public function __construct($expenses)
    {
        $this->expenses = $expenses;
    }

    public function collection()
    {
        return $this->expenses;
    }

    public function headings(): array
    {
        return [
            'Employee',
            'Category',
            'Amount',
            'Currency',
            'Date',
            'Description',
            'Status',
            'Rejection Reason',
        ];
    }

    public function map($expense): array
    {
        return [
            optional($expense->employee)->full_name ?? 'Unknown',
            ucfirst($expense->category),
            number_format((float) $expense->amount, 2),
            $expense->currency,
            $expense->expense_date->format('d-m-Y'),
            $expense->description ?? '-',
            ucfirst($expense->status),
            $expense->rejection_reason ?? '-',
        ];
    }
}
