<?php

namespace App\Http\Controllers\Admin\HomePage;

use App\Http\Controllers\Controller;
use App\Models\HomePage\Section;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
  public function storeFeature(Request $request)
  {
    return $this->redirectToSectionSettings($request);
  }

  public function updateFeature(Request $request)
  {
    if ($request->has('feature_section_status')) {
      $request->validate([
        'feature_section_status' => ['required', 'boolean'],
      ]);

      Section::query()->firstOrCreate([])->update([
        'feature_section_status' => $request->input('feature_section_status'),
      ]);

      $request->session()->flash('success', 'Featured service section status updated successfully!');

      return redirect()->back();
    }

    return $this->redirectToSectionSettings($request);
  }

  public function destroyFeature(Request $request, $id)
  {
    return $this->redirectToSectionSettings($request);
  }

  public function bulkDestroyFeature(Request $request)
  {
    return $this->redirectToSectionSettings($request);
  }

  private function redirectToSectionSettings(Request $request)
  {
    $request->session()->flash('warning', 'Featured service section items are managed from Featured Services.');

    return redirect()->back();
  }
}
