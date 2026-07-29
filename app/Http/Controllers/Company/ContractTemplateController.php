<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\ContractTemplate;
use Illuminate\Http\Request;

class ContractTemplateController extends Controller
{
    public function index()
    {
        $templates = ContractTemplate::query()
            ->with([
                'createdByUser',
                'updatedByUser',
            ])
            ->orderByDesc('is_default')
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(15);

        return view(
            'company.hr.contract-templates.index',
            compact('templates')
        );
    }

    public function create()
    {
        return view(
            'company.hr.contract-templates.create'
        );
    }

    public function store(Request $request)
    {
        $validated = $this->validateTemplate($request);

        $validated['is_default'] =
            $request->boolean('is_default');

        $validated['is_active'] =
            $request->boolean('is_active');

        $validated['created_by'] =
            auth('company')->id();

        $validated['updated_by'] =
            auth('company')->id();

        if ($validated['is_default']) {
            ContractTemplate::query()
                ->where('employment_type', $validated['employment_type'])
                ->update([
                    'is_default' => false,
                ]);
        }

        $template = ContractTemplate::create(
            $validated
        );

        return redirect()
            ->route(
                'company.hr.contract-templates.edit',
                $template
            )
            ->with(
                'success',
                'Contract template created successfully.'
            );
    }

    public function edit(
        ContractTemplate $contractTemplate
    ) {
        return view(
            'company.hr.contract-templates.edit',
            compact('contractTemplate')
        );
    }

    public function update(
        Request $request,
        ContractTemplate $contractTemplate
    ) {
        $validated = $this->validateTemplate(
            $request
        );

        $validated['is_default'] =
            $request->boolean('is_default');

        $validated['is_active'] =
            $request->boolean('is_active');

        $validated['updated_by'] =
            auth('company')->id();

        if ($validated['is_default']) {
            ContractTemplate::query()
                ->where(
                    'employment_type',
                    $validated['employment_type']
                )
                ->whereKeyNot($contractTemplate->id)
                ->update([
                    'is_default' => false,
                ]);
        }

        if (
            $contractTemplate->content !==
            $validated['content']
        ) {
            $validated['version'] =
                $contractTemplate->version + 1;
        }

        $contractTemplate->update($validated);

        return redirect()
            ->route(
                'company.hr.contract-templates.edit',
                $contractTemplate
            )
            ->with(
                'success',
                'Contract template updated successfully.'
            );
    }

    private function validateTemplate(
        Request $request
    ): array {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'employment_type' => [
                'nullable',
                'in:full_time,part_time,temporary,contractor,apprentice,zero_hours',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'content' => [
                'required',
                'string',
            ],
        ]);
    }
}