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
        'Type',
        'Category',
        'From',

        'Original Amount',
        'Returned Amount',
        'Final Amount',

        'Original Net',
        'Returned Net',
        'Final Net',

        'Original VAT',
        'Returned VAT',
        'Final VAT',

        'Currency',
        'Date',
        'Return Date',

        'Description',
        'Return Note',

        'Source',
        'Status',
        'Rejection Reason',
    ];
}

public function map($expense): array
{
    $originalAmount =
        (float) $expense->amount;

    $returnedAmount =
        (float) ($expense->return_amount ?? 0);

    $finalAmount =
        $originalAmount - $returnedAmount;


    $originalNet =
        (float) ($expense->net_amount ?? 0);

    $returnedNet =
        (float) ($expense->return_net_amount ?? 0);

    $finalNet =
        $originalNet - $returnedNet;


    $originalVat =
        (float) ($expense->vat_amount ?? 0);

    $returnedVat =
        (float) ($expense->return_vat_amount ?? 0);

    $finalVat =
        $originalVat - $returnedVat;


    return [

        optional(
            $expense->employee
        )->full_name ?? 'Unknown',

        ucfirst(
            $expense->expense_type === 'personal'
                ? 'Personal Money'
                : (
                    $expense->expense_type
                    ?? 'Expense'
                )
        ),

        ucfirst($expense->category),

        $expense->merchant ?? '-',

        number_format(
            $originalAmount,
            2,
            '.',
            ''
        ),

        number_format(
            $returnedAmount,
            2,
            '.',
            ''
        ),

        number_format(
            $finalAmount,
            2,
            '.',
            ''
        ),

        number_format(
            $originalNet,
            2,
            '.',
            ''
        ),

        number_format(
            $returnedNet,
            2,
            '.',
            ''
        ),

        number_format(
            $finalNet,
            2,
            '.',
            ''
        ),

        number_format(
            $originalVat,
            2,
            '.',
            ''
        ),

        number_format(
            $returnedVat,
            2,
            '.',
            ''
        ),

        number_format(
            $finalVat,
            2,
            '.',
            ''
        ),

        $expense->currency,

        $expense->expense_date
            ?->format('d-m-Y'),

        $expense->return_date
            ?->format('d-m-Y'),

        $expense->description ?? '-',

        $expense->return_note ?? '-',

        $expense->source === 'employee_app'
            ? 'App'
            : 'Admin',

        ucfirst($expense->status),

        $expense->rejection_reason ?? '-',
    ];
}
}
