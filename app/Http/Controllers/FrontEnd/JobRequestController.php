<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\Company\EmployeeJob;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JobRequestController extends Controller
{
    /**
     * Show the public Request a Job form.
     */
    public function create()
    {
        return view('frontend.request-job');
    }

    /**
     * Store a public Yes Trades customer job directly
     * in the Company Own employee_jobs system.
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Simple honeypot spam protection
        |--------------------------------------------------------------------------
        |
        | Real customers will never see/fill this field.
        |
        */
        if ($request->filled('website')) {
            return redirect()
                ->route('frontend.job_request.create')
                ->with(
                    'success',
                    'Thank you. Your job request has been received.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Validate PUBLIC customer fields only
        |--------------------------------------------------------------------------
        |
        | We intentionally do NOT accept:
        | - employee_id
        | - client_id
        | - client_property_id
        | - internal status
        | - payment status
        | - admin status
        |
        | Those values are controlled by the server below.
        |
        */
        $validated = $request->validate([
            'customer_name' => [
                'required',
                'string',
                'max:255',
            ],

            'customer_phone' => [
                'required',
                'string',
                'max:50',
            ],

            'customer_email' => [
                'required',
                'email',
                'max:255',
            ],

            'site_address' => [
                'required',
                'string',
                'max:500',
            ],

            'site_postcode' => [
                'required',
                'string',
                'max:20',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
                'max:5000',
            ],

            /*
             * This is only the customer's preferred date.
             * It is NOT treated as confirmed by Yes Trades.
             */
            'preferred_date' => [
                'nullable',
                'date',
                'after_or_equal:today',
            ],

            'preferred_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'urgency' => [
                'required',
                Rule::in([
                    'normal',
                    'urgent',
                    'emergency',
                ]),
            ],

            /*
             * Optional certificate information.
             */
            'certificate_required' => [
                'nullable',
                'boolean',
            ],

            'certificate_type' => [
                'nullable',
                'required_if:certificate_required,1',
                Rule::in(
                    EmployeeJob::CERTIFICATE_TYPES
                ),
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Convert customer urgency to internal job priority
        |--------------------------------------------------------------------------
        */
        $priorityMap = [
            'normal' => 'medium',
            'urgent' => 'high',
            'emergency' => 'urgent',
        ];

        $priority =
            $priorityMap[$validated['urgency']]
            ?? 'medium';

        /*
        |--------------------------------------------------------------------------
        | Appointment status
        |--------------------------------------------------------------------------
        |
        | If customer supplied a preferred date:
        | appointment_status = proposed
        |
        | If no date:
        | appointment_status = not_confirmed
        |
        | This means the customer cannot confirm an appointment themselves.
        |
        */
        $hasPreferredDate =
            !empty($validated['preferred_date']);

        $appointmentStatus =
            $hasPreferredDate
                ? 'proposed'
                : 'not_confirmed';

        /*
        |--------------------------------------------------------------------------
        | Certificate
        |--------------------------------------------------------------------------
        */
        $certificateRequired =
            $request->boolean('certificate_required');

        /*
        |--------------------------------------------------------------------------
        | Create the REAL Company Own job
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | client_id = null
        |     This is always Private / No Client Account.
        |
        | employee_id = null
        |     Office must review and assign an engineer.
        |
        | payment_required = true
        |     Prevents the invoice from being forgotten.
        |
        | admin_status is automatically recalculated by EmployeeJob model.
        |
        */
        $job = EmployeeJob::create([
            /*
             * Never associate a public submission with a
             * Company Client / Account.
             */
            'client_id' => null,
            'client_property_id' => null,

            /*
             * Always initially unassigned.
             */
            'employee_id' => null,

            /*
             * Customer/job details.
             */
            'title' =>
                trim($validated['title']),

            'description' =>
                trim($validated['description']),

            'customer_name' =>
                trim($validated['customer_name']),

            'customer_phone' =>
                trim($validated['customer_phone']),

            'customer_email' =>
                strtolower(
                    trim($validated['customer_email'])
                ),

            'site_address' =>
                trim($validated['site_address']),

            'site_postcode' =>
                strtoupper(
                    trim($validated['site_postcode'])
                ),

            /*
             * Customer preference only.
             */
            'appointment_status' =>
                $appointmentStatus,

            'scheduled_date' =>
                $hasPreferredDate
                    ? $validated['preferred_date']
                    : null,

            'scheduled_start_time' =>
                $hasPreferredDate
                    ? ($validated['preferred_time'] ?? null)
                    : null,

            /*
             * Internal priority.
             */
            'priority' =>
                $priority,

            /*
             * Your existing EmployeeJob model currently
             * uses "assigned" as its initial workflow status.
             * employee_id being NULL is what makes this
             * actually unassigned.
             */
            'status' =>
                'assigned',

            /*
             * Internal explanation so office staff know
             * exactly where this job came from.
             */
            'internal_notes' =>
                'Website job request - direct/private Yes Trades customer. '
                . 'Customer submitted this request online. '
                . 'Appointment has not been confirmed by the office.',

            /*
             * Direct Yes Trades customer:
             * invoice must be dealt with.
             */
            'payment_required' =>
                true,

            'payment_status' =>
                'invoice_required',

            /*
             * Certificate tracking.
             */
            'certificate_required' =>
                $certificateRequired,

            'certificate_type' =>
                $certificateRequired
                    ? $validated['certificate_type']
                    : null,

            'certificate_status' =>
                $certificateRequired
                    ? 'required'
                    : 'not_required',

            /*
             * Website requests always require office review.
             */
            'admin_status' =>
                'action_required',

            /*
             * Public customer is not a Company Portal user.
             * Database already allows these to be NULL.
             */
            'created_by' => null,
            'updated_by' => null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Success
        |--------------------------------------------------------------------------
        |
        | EmployeeJob automatically generates the YT reference
        | after creation.
        |
        */
        return redirect()
            ->route('frontend.job_request.create')
            ->with(
                'success',
                'Your job request has been received successfully.'
            )
            ->with(
                'job_reference',
                $job->job_reference
            );
    }
}