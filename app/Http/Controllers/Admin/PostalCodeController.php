<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostalCode;
use App\Models\Language;
use App\Models\Services\ServiceCategory;
use App\Models\Services\ServiceContent;
use App\Models\Services\ServicePackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PostalCodeController extends Controller
{
    public function index()
    {
        $postalCodes = PostalCode::orderBy('id', 'desc')->paginate(10);
        $defaultPostalPrice = DB::table('basic_settings')->where('uniqid', 12345)->value('postal_code_default_price');
        $languageId = Language::query()->where('is_default', 1)->value('id');
        $services = ServiceContent::query()
            ->where('language_id', $languageId)
            ->orderBy('name')
            ->get(['service_id', 'category_id', 'name']);
        $packages = ServicePackage::query()
            ->where('is_active', 1)
            ->orderBy('title')
            ->get(['id', 'title']);
        $categories = ServiceCategory::query()
            ->where('language_id', $languageId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.services.postal-codes.index', compact(
            'postalCodes',
            'defaultPostalPrice',
            'services',
            'packages',
            'categories'
        ));
    }

    public function store(Request $request)
    {
        $validator = $this->validator($request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        PostalCode::create($this->payload($request));

        return response()->json(['message' => 'Postal code surcharge added successfully!'], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = $this->validator($request, $id);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $postalCode = PostalCode::findOrFail($id);
        $postalCode->update($this->payload($request));

        return response()->json(['message' => 'Postal code surcharge updated successfully!'], 200);
    }

    private function validator(Request $request, $ignoreId = null)
    {
        $validator = Validator::make($request->all(), [
            'postal_code' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'apply_to' => 'nullable|in:all,service,package,category',
            'service_id' => 'nullable|required_if:apply_to,service|exists:services,id',
            'service_package_id' => 'nullable|required_if:apply_to,package|exists:service_packages,id',
            'service_category_id' => 'nullable|required_if:apply_to,category|exists:service_categories,id',
        ]);

        $validator->after(function ($validator) use ($request, $ignoreId) {
            $payload = $this->payload($request);

            if ($payload['normalized_postal_code'] === '') {
                return;
            }

            $duplicate = PostalCode::query()
                ->where('normalized_postal_code', $payload['normalized_postal_code'])
                ->where('service_id', $payload['service_id'])
                ->where('service_package_id', $payload['service_package_id'])
                ->where('service_category_id', $payload['service_category_id'])
                ->when($ignoreId, function ($query) use ($ignoreId) {
                    $query->where('id', '!=', $ignoreId);
                })
                ->exists();

            if ($duplicate) {
                $validator->errors()->add('postal_code', 'A surcharge rule already exists for this postcode area and target.');
            }
        });

        return $validator;
    }

    private function payload(Request $request): array
    {
        $applyTo = $request->input('apply_to', 'all');

        return [
            'postal_code' => trim((string) $request->input('postal_code')),
            'normalized_postal_code' => PostalCode::normalizePostcode($request->input('postal_code')),
            'price' => $request->input('price'),
            'service_id' => $applyTo === 'service' ? $request->input('service_id') : null,
            'service_package_id' => $applyTo === 'package' ? $request->input('service_package_id') : null,
            'service_category_id' => $applyTo === 'category' ? $request->input('service_category_id') : null,
        ];
    }

    public function updateDefaultPrice(Request $request)
    {
        $rules = [
            'default_price' => 'required|numeric|min:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::table('basic_settings')->updateOrInsert(
            ['uniqid' => 12345],
            ['postal_code_default_price' => $request->input('default_price')]
        );

        return response()->json(['message' => 'Default postal price updated successfully!'], 200);
    }

    public function destroy(Request $request, $id)
    {
        $postalCode = PostalCode::findOrFail($id);
        $postalCode->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Postal code deleted successfully!'], 200);
        }

        return redirect()->back()->with('success', 'Postal code deleted successfully!');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        PostalCode::whereIn('id', $ids)->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Postal codes deleted successfully!'], 200);
        }

        return redirect()->back()->with('success', 'Postal codes deleted successfully!');
    }
}
