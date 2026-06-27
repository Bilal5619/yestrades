<?php

namespace App\Http\Controllers\Admin\HomePage;

use App\Http\Controllers\Controller;
use App\Models\HomePage\Section;
use Illuminate\Http\Request;

class CategorySectionController extends Controller
{
  public function update(Request $request)
  {
    $request->validate([
      'category_section_status' => ['required', 'boolean'],
    ]);

    Section::query()->firstOrCreate([])->update([
      'category_section_status' => $request->input('category_section_status'),
    ]);

    $request->session()->flash('success', 'Category section status updated successfully!');

    return redirect()->back();
  }
}
