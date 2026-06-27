<?php

namespace App\Http\Controllers\Admin\HomePage;

use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Models\Admin\HeroSliderImage;
use App\Models\BasicSettings\Basic;
use Illuminate\Http\Request;

class AdvertisementSliderController extends Controller
{
    public function index()
    {
        $settings = Basic::first();
        $images   = HeroSliderImage::orderBy('sort_order')->get();

        return view('admin.home-page.advertisement-slider', compact('settings', 'images'));
    }

    public function updateLimit(Request $request)
    {
        $request->validate(['ad_slider_max_images' => ['required', 'integer', 'min:1', 'max:50']]);

        $newLimit  = (int) $request->input('ad_slider_max_images');
        $existing  = HeroSliderImage::count();

        if ($newLimit < $existing) {
            return redirect()->back()
                ->with('limit_error', "Cannot set limit to {$newLimit}: you currently have {$existing} image(s) uploaded. Please delete some images first, then lower the limit.");
        }

        Basic::first()->update(['ad_slider_max_images' => $newLimit]);

        return redirect()->back()->with('limit_success', 'Image limit updated successfully.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'images'   => ['required', 'array', 'min:1'],
            'images.*' => ['file', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
        ]);

        $limit    = (int) (Basic::first()->ad_slider_max_images ?? 10);
        $existing = HeroSliderImage::count();
        $incoming = count($request->file('images'));

        if ($existing >= $limit) {
            return redirect()->back()
                ->with('upload_error', "You have reached the maximum of {$limit} advertisement images.");
        }
        if ($existing + $incoming > $limit) {
            $allowed = $limit - $existing;
            return redirect()->back()
                ->with('upload_error', "Only {$allowed} more image(s) can be added (limit is {$limit}).");
        }

        $maxOrder = HeroSliderImage::max('sort_order') ?? 0;
        foreach ($request->file('images') as $file) {
            $filename = UploadFile::store(public_path('assets/img/hero/'), $file);
            HeroSliderImage::create(['image' => $filename, 'sort_order' => ++$maxOrder]);
        }

        return redirect()->back()->with('upload_success', "Uploaded {$incoming} image(s) successfully.");
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('delete_ids', []);

        if (empty($ids)) {
            return redirect()->back()->with('delete_info', 'No images were selected for deletion.');
        }

        $records = HeroSliderImage::whereIn('id', $ids)->get();

        foreach ($records as $record) {
            $path = public_path('assets/img/hero/' . $record->image);
            if (file_exists($path)) {
                @unlink($path);
            }
            $record->delete();
        }

        return redirect()->back()->with('delete_success', count($records) . ' image(s) deleted.');
    }
}
