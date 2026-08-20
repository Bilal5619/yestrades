<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\Employee;
use App\Models\Company\EmployeeDocument;
use App\Models\Company\EmployeeDocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeDocumentController extends Controller
{
    public function index(Employee $employee)
    {
        // Ensure the employee has the standard required checklist.
        $employee->syncRequiredDocumentChecklist();
        $documents = $employee->documents()
            ->with([
                'documentType',
                'uploadedBy',
                'reviewedBy',
            ])
            ->get()
            ->sortBy(function ($document) {
                return $document->documentType->name ?? '';
            });

        $documentTypes = EmployeeDocumentType::query()
            ->where('status', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $summary = [
            'missing' => $employee->missingDocuments()->count(),
            'expiring' => $employee->expiringDocuments()->count(),
            'expired' => $employee->expiredDocuments()->count(),
            'pending_review' => $employee->documents()
                ->where('review_status', 'pending_review')
                ->count(),
        ];

        return view(
            'company.hr.employees.documents.index',
            compact(
                'employee',
                'documents',
                'documentTypes',
                'summary'
            )
        );
    }

    public function store(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'document_type_id' => [
                'required',
                'integer',
                'exists:employee_document_types,id',
            ],
            'title' => [
                'nullable',
                'string',
                'max:255',
            ],
            'document_number' => [
                'nullable',
                'string',
                'max:150',
            ],
            'issue_date' => [
                'nullable',
                'date',
            ],
            'expiry_date' => [
                'nullable',
                'date',
                'after_or_equal:issue_date',
            ],
            'file' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp,doc,docx',
                'max:10240',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:3000',
            ],
        ]);

        $documentType = EmployeeDocumentType::findOrFail(
            $validated['document_type_id']
        );

        if (
            $documentType->requires_expiry_date &&
            empty($validated['expiry_date'])
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'expiry_date' =>
                        'An expiry date is required for this document type.',
                ]);
        }

            $existingDocument = $employee->documents()
                ->where(
                    'document_type_id',
                    $documentType->id
                )
                ->first();

            if (
                $existingDocument &&
                $existingDocument->file_path
            ) {
                if (Storage::disk('local')->exists(
        $existingDocument->file_path
    )) {
        Storage::disk('local')->delete(
            $existingDocument->file_path
        );
    } elseif (Storage::disk('public')->exists(
        $existingDocument->file_path
    )) {
        Storage::disk('public')->delete(
            $existingDocument->file_path
        );
    }
        }

        $uploadedFile = $request->file('file');

       $filePath = $uploadedFile->store(
    'company-private/employees/' .
    $employee->id .
    '/documents',
    'local'
);

        $reviewStatus = $documentType->requires_review
            ? 'pending_review'
            : 'approved';

        $document = EmployeeDocument::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'document_type_id' => $documentType->id,
            ],
            [
                'title' => $validated['title']
                    ?: $documentType->name,

                'document_number' =>
                    $validated['document_number'] ?? null,

                'file_path' => $filePath,

                'original_file_name' =>
                    $uploadedFile->getClientOriginalName(),

                'file_type' =>
                    $uploadedFile->getClientMimeType(),

                'file_size' =>
                    $uploadedFile->getSize(),

                'issue_date' =>
                    $validated['issue_date'] ?? null,

                'expiry_date' =>
                    $validated['expiry_date'] ?? null,

                'is_required' =>
                    $existingDocument
                        ? $existingDocument->is_required
                        : $documentType->is_required_by_default,

                'review_status' => $reviewStatus,
                'review_notes' => null,
                'notes' => $validated['notes'] ?? null,

                'uploaded_by' => auth('company')->id(),
                'uploaded_at' => now(),

                'reviewed_by' => null,
                'reviewed_at' => null,

                'created_by' =>
                    $existingDocument
                        ? $existingDocument->created_by
                        : auth('company')->id(),

                'updated_by' => auth('company')->id(),
            ]
        );

        return redirect()
            ->route(
                'company.hr.employees.documents.index',
                $employee
            )
            ->with(
                'success',
                $documentType->name .
                ' uploaded successfully and is awaiting review.'
            );
    }

    public function approve(
        Employee $employee,
        EmployeeDocument $document
    ) {
        $this->ensureDocumentBelongsToEmployee(
            $employee,
            $document
        );

        if (!$document->file_path) {
            return back()->withErrors([
                'document' =>
                    'A missing document cannot be approved.',
            ]);
        }

        $document->update([
            'review_status' => 'approved',
            'review_notes' => null,
            'reviewed_by' => auth('company')->id(),
            'reviewed_at' => now(),
            'updated_by' => auth('company')->id(),
        ]);

        return back()->with(
            'success',
            'Document approved successfully.'
        );
    }

    public function reject(
        Request $request,
        Employee $employee,
        EmployeeDocument $document
    ) {
        $this->ensureDocumentBelongsToEmployee(
            $employee,
            $document
        );

        $validated = $request->validate([
            'review_notes' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        $document->update([
            'review_status' => 'rejected',
            'review_notes' => $validated['review_notes'],
            'reviewed_by' => auth('company')->id(),
            'reviewed_at' => now(),
            'updated_by' => auth('company')->id(),
        ]);

        return back()->with(
            'success',
            'Document rejected. The reason has been recorded.'
        );
    }

    private function ensureDocumentBelongsToEmployee(
        Employee $employee,
        EmployeeDocument $document
    ): void {
        abort_unless(
            $document->employee_id === $employee->id,
            404
        );
    }

    public function view(
    Employee $employee,
    EmployeeDocument $document
) {
    $this->ensureDocumentBelongsToEmployee(
        $employee,
        $document
    );

    if (!$document->file_path) {
        abort(404);
    }

    $disk = $this->documentDisk($document);

    abort_unless(
        Storage::disk($disk)->exists($document->file_path),
        404
    );

    $absolutePath = Storage::disk($disk)
        ->path($document->file_path);

    return response()->file(
        $absolutePath,
        [
            'Content-Disposition' =>
                'inline; filename="' .
                basename(
                    $document->original_file_name
                        ?: $document->file_path
                ) .
                '"',
        ]
    );
}

public function download(
    Employee $employee,
    EmployeeDocument $document
) {
    $this->ensureDocumentBelongsToEmployee(
        $employee,
        $document
    );

    if (!$document->file_path) {
        abort(404);
    }

    $disk = $this->documentDisk($document);

    abort_unless(
        Storage::disk($disk)->exists($document->file_path),
        404
    );

    return Storage::disk($disk)->download(
        $document->file_path,
        $document->original_file_name
            ?: basename($document->file_path)
    );
}

private function documentDisk(
    EmployeeDocument $document
): string {
    if (
        Storage::disk('local')->exists(
            $document->file_path
        )
    ) {
        return 'local';
    }

    return 'public';
}
public function expiring(Request $request)
{
    $today = now()->startOfDay();
    $expiryLimit = now()->addDays(30)->endOfDay();

    $query = EmployeeDocument::query()
        ->with([
            'employee',
            'documentType',
        ])
        ->whereNotNull('file_path')
        ->whereNotNull('expiry_date')
        ->whereDate('expiry_date', '<=', $expiryLimit)
        ->whereHas('employee', function ($employeeQuery) {
            $employeeQuery->whereNotIn(
                'employment_status',
                [
                    'archived',
                    'left_company',
                ]
            );
        });

    if ($request->filled('search')) {
        $search = trim($request->search);

        $query->where(function ($documentQuery) use ($search) {
            $documentQuery
                ->where('document_number', 'like', "%{$search}%")
                ->orWhere('title', 'like', "%{$search}%")
                ->orWhereHas(
                    'documentType',
                    function ($typeQuery) use ($search) {
                        $typeQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    }
                )
                ->orWhereHas(
                    'employee',
                    function ($employeeQuery) use ($search) {
                        $employeeQuery
                            ->where(
                                'employee_number',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
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
                                'department',
                                'like',
                                "%{$search}%"
                            );
                    }
                );
        });
    }

    if ($request->filled('department')) {
        $department = $request->department;

        $query->whereHas(
            'employee',
            function ($employeeQuery) use ($department) {
                $employeeQuery->where(
                    'department',
                    $department
                );
            }
        );
    }

    if ($request->filled('expiry_status')) {
        if ($request->expiry_status === 'expired') {
            $query->whereDate(
                'expiry_date',
                '<',
                $today
            );
        }

        if ($request->expiry_status === 'expiring_soon') {
            $query
                ->whereDate(
                    'expiry_date',
                    '>=',
                    $today
                )
                ->whereDate(
                    'expiry_date',
                    '<=',
                    $expiryLimit
                );
        }
    }

    $documents = $query
        ->orderBy('expiry_date')
        ->paginate(20)
        ->withQueryString();

    $departments = Employee::query()
        ->whereNotNull('department')
        ->where('department', '!=', '')
        ->whereNotIn(
            'employment_status',
            [
                'archived',
                'left_company',
            ]
        )
        ->distinct()
        ->orderBy('department')
        ->pluck('department');

    $expiredCount = EmployeeDocument::query()
        ->whereNotNull('file_path')
        ->whereNotNull('expiry_date')
        ->whereDate('expiry_date', '<', $today)
        ->whereHas('employee', function ($employeeQuery) {
            $employeeQuery->whereNotIn(
                'employment_status',
                [
                    'archived',
                    'left_company',
                ]
            );
        })
        ->count();

    $expiringCount = EmployeeDocument::query()
        ->whereNotNull('file_path')
        ->whereNotNull('expiry_date')
        ->whereDate('expiry_date', '>=', $today)
        ->whereDate('expiry_date', '<=', $expiryLimit)
        ->whereHas('employee', function ($employeeQuery) {
            $employeeQuery->whereNotIn(
                'employment_status',
                [
                    'archived',
                    'left_company',
                ]
            );
        })
        ->count();

    return view(
        'company.hr.employees.expiring-documents',
        compact(
            'documents',
            'departments',
            'expiredCount',
            'expiringCount'
        )
    );
}
}