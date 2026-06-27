<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Models\Language;
use App\Models\Services\ServiceContent;
use App\Models\Services\ServicePackage;
use App\Models\Services\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Purifier;

class ServicePackageController extends Controller
{
  public function index()
  {
    $packages = ServicePackage::with('services.content')
      ->orderBy('renewal_section_sort_order')
      ->orderByDesc('id')
      ->paginate(15);

    return view('admin.service-packages.index', compact('packages'));
  }

  public function create()
  {
    return view('admin.service-packages.create', [
      'package' => new ServicePackage(),
      'services' => $this->serviceOptions(),
      'selectedServices' => [],
    ]);
  }

  public function store(Request $request)
  {
    $data = $this->validatedData($request);

    DB::transaction(function () use ($request, $data) {
      if ($request->hasFile('image')) {
        $data['image'] = UploadFile::store(public_path('assets/img/services/'), $request->file('image'));
      }

      $package = ServicePackage::create($data);
      $this->syncServices($package, $request->input('services', []));
    });

    return redirect()->route('admin.service_packages.index')->with('success', 'Package created successfully!');
  }

  public function edit($id)
  {
    $package = ServicePackage::with('services')->findOrFail($id);

    return view('admin.service-packages.edit', [
      'package' => $package,
      'services' => $this->serviceOptions(),
      'selectedServices' => $package->services->pluck('id')->map(fn ($id) => (string) $id)->all(),
    ]);
  }

  public function update(Request $request, $id)
  {
    $package = ServicePackage::findOrFail($id);
    $data = $this->validatedData($request, $package->id);

    DB::transaction(function () use ($request, $package, $data) {
      if ($request->hasFile('image')) {
        $data['image'] = UploadFile::update(public_path('assets/img/services/'), $request->file('image'), $package->image);
      }

      $package->update($data);
      $this->syncServices($package, $request->input('services', []));
    });

    return redirect()->route('admin.service_packages.index')->with('success', 'Package updated successfully!');
  }

  public function destroy($id)
  {
    $package = ServicePackage::findOrFail($id);

    if ($package->image) {
      @unlink(public_path('assets/img/services/' . $package->image));
    }

    $package->delete();

    return redirect()->back()->with('success', 'Package deleted successfully!');
  }

  private function validatedData(Request $request, ?int $ignoreId = null): array
  {
    $data = $request->validate([
      'title' => ['required', 'string', 'max:255'],
      'slug' => ['required', 'string', 'max:255', Rule::unique('service_packages', 'slug')->ignore($ignoreId)],
      'short_description' => ['nullable', 'string', 'max:1000'],
      'description' => ['nullable', 'string'],
      'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg'],
      'price' => ['required', 'numeric', 'min:0'],
      'offer_price' => ['nullable', 'numeric', 'min:0'],
      'offer_label' => ['nullable', 'string', 'max:255'],
      'price_placeholder' => ['nullable', 'string', 'max:255'],
      'is_active' => ['required', 'boolean'],
      'show_in_renewal_section' => ['required', 'boolean'],
      'renewal_section_sort_order' => ['nullable', 'integer'],
      'services' => ['required', 'array', 'min:1'],
      'services.*' => ['integer', 'exists:services,id'],
    ]);

    $data['description'] = Purifier::clean($data['description'] ?? null);
    $data['renewal_section_sort_order'] = $data['renewal_section_sort_order'] ?? 0;

    unset($data['services']);

    return $data;
  }

  private function syncServices(ServicePackage $package, array $serviceIds): void
  {
    $sync = [];

    foreach (array_values(array_unique($serviceIds)) as $index => $serviceId) {
      $sync[$serviceId] = ['sort_order' => $index + 1];
    }

    $package->services()->sync($sync);
  }

  private function serviceOptions()
  {
    $language = Language::where('is_default', 1)->first();
    $names = ServiceContent::where('language_id', optional($language)->id)
      ->pluck('name', 'service_id');

    return Services::where('status', 1)
      ->orderByDesc('id')
      ->get()
      ->map(function ($service) use ($names) {
        $service->display_name = $names[$service->id] ?? 'Service #' . $service->id;
        return $service;
      });
  }
}
