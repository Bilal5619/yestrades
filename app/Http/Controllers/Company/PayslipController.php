<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\Employee;
use App\Models\Company\EmployeePayslip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PayslipController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeePayslip::query()
            ->with([
                'employee',
                'uploadedByUser',
            ]);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($payslipQuery) use ($search) {
                $payslipQuery
                    ->where(
                        'pay_period',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'original_filename',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhereHas(
                        'employee',
                        function ($employeeQuery) use ($search) {
                            $employeeQuery
                                ->where(
                                    'first_name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'last_name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'employee_number',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
            });
        }

        if ($request->filled('employee_id')) {
            $query->where(
                'employee_id',
                $request->employee_id
            );
        }

        if ($request->filled('year')) {
            $query->whereYear(
                'payment_date',
                $request->year
            );
        }

        $payslips = $query
            ->orderByDesc('payment_date')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        /*
         * Employee uses SoftDeletes, so deleted employees
         * are automatically excluded.
         */
        $employees = Employee::query()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $years = EmployeePayslip::query()
            ->selectRaw('YEAR(payment_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $stats = [
            'total' => EmployeePayslip::count(),

            'this_month' => EmployeePayslip::query()
                ->whereYear(
                    'payment_date',
                    now()->year
                )
                ->whereMonth(
                    'payment_date',
                    now()->month
                )
                ->count(),

            'employees' => EmployeePayslip::query()
                ->distinct()
                ->count('employee_id'),

            'this_year' => EmployeePayslip::query()
                ->whereYear(
                    'payment_date',
                    now()->year
                )
                ->count(),
        ];

        return view(
            'company.hr.payslips.index',
            compact(
                'payslips',
                'employees',
                'years',
                'stats'
            )
        );
    }

    public function create(Request $request)
    {
        $employees = Employee::query()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $selectedEmployee = null;

        if ($request->filled('employee_id')) {
            $selectedEmployee = Employee::find(
                $request->employee_id
            );
        }

        return view(
            'company.hr.payslips.create',
            compact(
                'employees',
                'selectedEmployee'
            )
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => [
                'required',
                'exists:employees,id',
            ],

            'pay_period' => [
                'required',
                'string',
                'max:100',
            ],

            'payment_date' => [
                'required',
                'date',
            ],

            'payslip_file' => [
                'required',
                'file',
                'mimes:pdf',
                'max:10240',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $employee = Employee::findOrFail(
            $validated['employee_id']
        );

        $file = $request->file('payslip_file');

        $storedFilename =
            Str::uuid()->toString() . '.pdf';

        $directory =
            'company-private/payslips/'
            . $employee->id;

        $filePath = $file->storeAs(
            $directory,
            $storedFilename,
            'local'
        );

        EmployeePayslip::create([
            'employee_id' => $employee->id,

            'pay_period' =>
                $validated['pay_period'],

            'payment_date' =>
                $validated['payment_date'],

            'original_filename' =>
                $file->getClientOriginalName(),

            'stored_filename' =>
                $storedFilename,

            'file_path' =>
                $filePath,

            'mime_type' =>
                $file->getMimeType(),

            'file_size' =>
                $file->getSize(),

            'notes' =>
                $validated['notes'] ?? null,

            'uploaded_by' =>
                auth('company')->id(),
        ]);

        return redirect()
            ->route(
                'company.hr.payslips.index'
            )
            ->with(
                'success',
                'Payslip uploaded successfully.'
            );
    }

    public function history(
        Request $request,
        Employee $employee
    ) {
        $query = $employee->payslips()
            ->with('uploadedByUser');

        if ($request->filled('year')) {
            $query->whereYear(
                'payment_date',
                $request->year
            );
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($payslipQuery) use ($search) {
                $payslipQuery
                    ->where(
                        'pay_period',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'original_filename',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        $payslips = $query
            ->orderByDesc('payment_date')
            ->paginate(20)
            ->withQueryString();

        $years = $employee->payslips()
            ->selectRaw('YEAR(payment_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view(
            'company.hr.payslips.history',
            compact(
                'employee',
                'payslips',
                'years'
            )
        );
    }

    public function download(
        EmployeePayslip $payslip
    ) {
        abort_unless(
            Storage::disk('local')->exists(
                $payslip->file_path
            ),
            404,
            'Payslip file could not be found.'
        );

        return Storage::disk('local')->download(
            $payslip->file_path,
            $payslip->original_filename,
            [
                'Content-Type' =>
                    $payslip->mime_type
                    ?: 'application/pdf',
            ]
        );
    }

    public function replaceForm(
        EmployeePayslip $payslip
    ) {
        $payslip->load('employee');

        return view(
            'company.hr.payslips.replace',
            compact('payslip')
        );
    }

    public function replace(
        Request $request,
        EmployeePayslip $payslip
    ) {
        $validated = $request->validate([
            'pay_period' => [
                'required',
                'string',
                'max:100',
            ],

            'payment_date' => [
                'required',
                'date',
            ],

            'payslip_file' => [
                'nullable',
                'file',
                'mimes:pdf',
                'max:10240',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $updateData = [
            'pay_period' =>
                $validated['pay_period'],

            'payment_date' =>
                $validated['payment_date'],

            'notes' =>
                $validated['notes'] ?? null,
        ];

        if ($request->hasFile('payslip_file')) {
            $file = $request->file('payslip_file');

            $storedFilename =
                Str::uuid()->toString() . '.pdf';

            $directory =
                'company-private/payslips/'
                . $payslip->employee_id;

            $newFilePath = $file->storeAs(
                $directory,
                $storedFilename,
                'local'
            );

            if (
                Storage::disk('local')->exists(
                    $payslip->file_path
                )
            ) {
                Storage::disk('local')->delete(
                    $payslip->file_path
                );
            }

            $updateData['original_filename'] =
                $file->getClientOriginalName();

            $updateData['stored_filename'] =
                $storedFilename;

            $updateData['file_path'] =
                $newFilePath;

            $updateData['mime_type'] =
                $file->getMimeType();

            $updateData['file_size'] =
                $file->getSize();

            $updateData['uploaded_by'] =
                auth('company')->id();
        }

        $payslip->update($updateData);

        return redirect()
            ->route(
                'company.hr.payslips.index'
            )
            ->with(
                'success',
                'Payslip updated successfully.'
            );
    }

    public function destroy(
        EmployeePayslip $payslip
    ) {
        if (
            $payslip->file_path &&
            Storage::disk('local')->exists(
                $payslip->file_path
            )
        ) {
            Storage::disk('local')->delete(
                $payslip->file_path
            );
        }

        $payslip->delete();

        return redirect()
            ->back()
            ->with(
                'success',
                'Payslip deleted successfully.'
            );
    }
}