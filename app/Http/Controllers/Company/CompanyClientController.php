<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\CompanyClient;
use App\Models\Company\CompanyClientProperty;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyClientController extends Controller
{
    public function index(Request $request)
    {
        $query = CompanyClient::query()
            ->withCount([
                'properties',
                'jobs',
            ]);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'contact_name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'email',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'phone',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'postcode',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        if ($request->filled('status')) {
            $query->where(
                'is_active',
                $request->input('status')
                    === 'active'
            );
        }

        $clients = $query
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view(
            'company.clients.index',
            compact('clients')
        );
    }

    public function create()
    {
        return view(
            'company.clients.create'
        );
    }

    public function store(Request $request)
    {
        $data =
            $this->validateClient($request);

        $data['created_by'] =
            auth('company')->id();

        $data['updated_by'] =
            auth('company')->id();

        $data['is_active'] =
            $request->boolean(
                'is_active',
                true
            );

        $client =
            CompanyClient::create($data);

        return redirect()
            ->route(
                'company.clients.show',
                $client
            )
            ->with(
                'success',
                'Client account created successfully.'
            );
    }

    public function show(
        CompanyClient $client
    ) {
        $client->load([
            'properties' => function ($query) {
                $query
                    ->orderByDesc('is_active')
                    ->orderBy('postcode');
            },

            'jobs' => function ($query) {
                $query
                    ->with([
                        'employee',
                        'property',
                    ])
                    ->orderByDesc(
                        'scheduled_date'
                    )
                    ->orderByDesc(
                        'scheduled_start_time'
                    )
                    ->limit(50);
            },
        ]);

        $stats = [
            'total_jobs' =>
                $client
                    ->jobs()
                    ->count(),

            'open_jobs' =>
                $client
                    ->jobs()
                    ->where(
                        'admin_status',
                        '!=',
                        'closed'
                    )
                    ->where(
                        'status',
                        '!=',
                        'cancelled'
                    )
                    ->count(),

            'outstanding_payment' =>
                $client
                    ->jobs()
                    ->where(
                        'payment_required',
                        true
                    )
                    ->whereNotIn(
                        'payment_status',
                        [
                            'paid',
                            'not_required',
                        ]
                    )
                    ->sum(
                        'agreed_amount'
                    ),

            'certificates_pending' =>
                $client
                    ->jobs()
                    ->where(
                        'certificate_required',
                        true
                    )
                    ->where(
                        'certificate_status',
                        '!=',
                        'completed'
                    )
                    ->count(),
        ];

        return view(
            'company.clients.show',
            compact(
                'client',
                'stats'
            )
        );
    }

    public function edit(
        CompanyClient $client
    ) {
        return view(
            'company.clients.edit',
            compact('client')
        );
    }

    public function update(
        Request $request,
        CompanyClient $client
    ) {
        $data =
            $this->validateClient(
                $request
            );

        $data['updated_by'] =
            auth('company')->id();

        $data['is_active'] =
            $request->boolean(
                'is_active'
            );

        $client->update($data);

        return redirect()
            ->route(
                'company.clients.show',
                $client
            )
            ->with(
                'success',
                'Client account updated successfully.'
            );
    }

    public function createProperty(
        CompanyClient $client
    ) {
        return view(
            'company.clients.properties.create',
            compact('client')
        );
    }

    public function storeProperty(
        Request $request,
        CompanyClient $client
    ) {
        $data =
            $this->validateProperty(
                $request
            );

        $data['is_active'] =
            $request->boolean(
                'is_active',
                true
            );

        $client
            ->properties()
            ->create($data);

        return redirect()
            ->route(
                'company.clients.show',
                $client
            )
            ->with(
                'success',
                'Property added successfully.'
            );
    }

    public function editProperty(
        CompanyClient $client,
        CompanyClientProperty $property
    ) {
        abort_unless(
            $property->client_id
                === $client->id,
            404
        );

        return view(
            'company.clients.properties.edit',
            compact(
                'client',
                'property'
            )
        );
    }

    public function updateProperty(
        Request $request,
        CompanyClient $client,
        CompanyClientProperty $property
    ) {
        abort_unless(
            $property->client_id
                === $client->id,
            404
        );

        $data =
            $this->validateProperty(
                $request
            );

        $data['is_active'] =
            $request->boolean(
                'is_active'
            );

        $property->update($data);

        return redirect()
            ->route(
                'company.clients.show',
                $client
            )
            ->with(
                'success',
                'Property updated successfully.'
            );
    }

    public function destroyProperty(
        CompanyClient $client,
        CompanyClientProperty $property
    ) {
        abort_unless(
            $property->client_id
                === $client->id,
            404
        );

        if (
            $property
                ->jobs()
                ->exists()
        ) {
            $property->update([
                'is_active' => false,
            ]);

            return redirect()
                ->route(
                    'company.clients.show',
                    $client
                )
                ->with(
                    'warning',
                    'This property has job history, so it was archived instead of deleted.'
                );
        }

        $property->delete();

        return redirect()
            ->route(
                'company.clients.show',
                $client
            )
            ->with(
                'success',
                'Property removed successfully.'
            );
    }

    private function validateClient(
        Request $request
    ): array {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'client_type' => [
                'required',

                Rule::in([
                    'company',
                    'letting_agent',
                    'estate_agent',
                    'contractor',
                    'landlord',
                    'property_manager',
                    'private_regular',
                    'other',
                ]),
            ],

            'contact_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:80',
            ],

            'address_line_1' => [
                'nullable',
                'string',
                'max:255',
            ],

            'address_line_2' => [
                'nullable',
                'string',
                'max:255',
            ],

            'city' => [
                'nullable',
                'string',
                'max:120',
            ],

            'county' => [
                'nullable',
                'string',
                'max:120',
            ],

            'postcode' => [
                'nullable',
                'string',
                'max:20',
            ],

            'payment_terms' => [
                'nullable',
                'string',
                'max:120',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);
    }

    private function validateProperty(
        Request $request
    ): array {
        return $request->validate([
            'property_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'address_line_1' => [
                'required',
                'string',
                'max:255',
            ],

            'address_line_2' => [
                'nullable',
                'string',
                'max:255',
            ],

            'city' => [
                'nullable',
                'string',
                'max:120',
            ],

            'county' => [
                'nullable',
                'string',
                'max:120',
            ],

            'postcode' => [
                'nullable',
                'string',
                'max:20',
            ],

            'contact_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'contact_phone' => [
                'nullable',
                'string',
                'max:80',
            ],

            'contact_email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'access_notes' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);
    }
}