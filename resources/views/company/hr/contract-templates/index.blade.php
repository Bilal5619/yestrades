@extends('company.layouts.app')

@section('title', 'Contract Templates')

@section('content')

@include(
    'company.hr.contract-templates.partials.styles'
)

<style>
    .contract-template-list-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .contract-template-list-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .contract-template-list-header p {
        margin: 0;
        color: #aab4ca;
    }

    .contract-template-add {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 16px;
        border-radius: 7px;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
    }

    .contract-template-table-wrapper {
        overflow-x: auto;
    }

    .contract-template-table {
        width: 100%;
        min-width: 900px;
        border-collapse: collapse;
    }

    .contract-template-table th,
    .contract-template-table td {
        padding: 13px 10px;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        text-align: left;
    }

    .contract-template-table th {
        color: #aab4ca;
        font-size: 12px;
    }

    .contract-template-badge {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 13px;
        font-size: 12px;
    }

    .contract-template-badge-active {
        background: rgba(46,191,114,0.16);
        color: #59dc96;
    }

    .contract-template-badge-inactive {
        background: rgba(170,180,202,0.14);
        color: #c4ccdc;
    }

    .contract-template-badge-default {
        background: rgba(255,179,71,0.16);
        color: #ffc46f;
    }

    .contract-template-edit {
        display: inline-flex;
        padding: 7px 11px;
        border-radius: 6px;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
    }

    .contract-template-empty {
        padding: 35px;
        text-align: center;
        color: #aab4ca;
    }
</style>

<div class="contract-template-list-header">

    <div>
        <h1>Contract Templates</h1>

        <p>
            Create and manage reusable employee contract templates.
        </p>
    </div>

    @if (
        auth('company')->user()
            ->hasPermission(
                'contract_templates.manage'
            )
    )
        <a href="{{ route(
                'company.hr.contract-templates.create'
            ) }}"
           class="contract-template-add">

            <i class="fas fa-plus"></i>
            Add Template
        </a>
    @endif

</div>

<div class="contract-template-card">

    <div class="contract-template-table-wrapper">

        <table class="contract-template-table">

            <thead>
                <tr>
                    <th>Name</th>
                    <th>Employment Type</th>
                    <th>Version</th>
                    <th>Default</th>
                    <th>Status</th>
                    <th>Updated</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($templates as $template)

                    <tr>
                        <td>
                            {{ $template->name }}
                        </td>

                        <td>
                            {{ $template->employment_type
                                ? ucwords(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $template->employment_type
                                    )
                                )
                                : 'All Types'
                            }}
                        </td>

                        <td>
                            v{{ $template->version }}
                        </td>

                        <td>
                            @if ($template->is_default)
                                <span class="
                                    contract-template-badge
                                    contract-template-badge-default
                                ">
                                    Default
                                </span>
                            @else
                                —
                            @endif
                        </td>

                        <td>
                            <span class="
                                contract-template-badge
                                {{ $template->is_active
                                    ? 'contract-template-badge-active'
                                    : 'contract-template-badge-inactive'
                                }}
                            ">
                                {{ $template->is_active
                                    ? 'Active'
                                    : 'Inactive'
                                }}
                            </span>
                        </td>

                        <td>
                            {{ optional(
                                $template->updated_at
                            )->format('d M Y H:i') }}
                        </td>

                        <td>
                            @if (
                                auth('company')->user()
                                    ->hasPermission(
                                        'contract_templates.manage'
                                    )
                            )
                                <a href="{{ route(
                                        'company.hr.contract-templates.edit',
                                        $template
                                    ) }}"
                                   class="contract-template-edit">
                                    Edit
                                </a>
                            @else
                                —
                            @endif
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="7">
                            <div class="contract-template-empty">
                                No contract templates found.
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div style="margin-top:20px;">
        {{ $templates->links() }}
    </div>

</div>

@endsection