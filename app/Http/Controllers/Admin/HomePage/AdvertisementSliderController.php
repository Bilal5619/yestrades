<?php

namespace App\Http\Controllers\Admin\HomePage;

use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Models\Admin\HeroSliderImage;
use App\Models\BasicSettings\Basic;
use App\Models\Services\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AdvertisementSliderController extends Controller
{
    public function index()
    {
        $settings = Basic::first();
        $images   = HeroSliderImage::orderBy('sort_order')->get();

        // Services the admin can advertise in the home page hero slider.
        $services = DB::table('services')
            ->join('service_contents', 'services.id', '=', 'service_contents.service_id')
            ->select('services.id', 'service_contents.name')
            ->orderBy('service_contents.name')
            ->get();

        return view('admin.home-page.advertisement-slider', compact('settings', 'images', 'services'));
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

    /**
     * Optional text + link shown on each slide of the home page hero slider.
     * A slide with no title is shown as a plain image (like a promo banner).
     */
    /**
     * Add a slide that advertises a service. No upload needed — the slider uses
     * the service's own image, name and price, and links to its booking page.
     */
    public function storeService(Request $request)
    {
        $request->validate(['service_id' => ['required', 'integer', 'exists:services,id']]);

        $limit    = (int) (Basic::first()->ad_slider_max_images ?? 10);
        $existing = HeroSliderImage::count();

        if ($existing >= $limit) {
            return redirect()->back()->with('upload_error', "You have reached the maximum of {$limit} slides. Delete one first, or raise the limit above.");
        }

        if (HeroSliderImage::where('service_id', $request->service_id)->exists()) {
            return redirect()->back()->with('upload_error', 'That service is already in the slider.');
        }

        HeroSliderImage::create([
            'service_id' => (int) $request->service_id,
            'sort_order' => (int) (HeroSliderImage::max('sort_order') ?? 0) + 1,
        ]);

        return redirect()->back()->with('upload_success', 'Service added to the home page slider.');
    }

    public function updateDetails(Request $request)    {
        $request->validate([
            'slides'                => ['required', 'array'],
            'slides.*.title'        => ['nullable', 'string', 'max:120'],
            'slides.*.subtitle'     => ['nullable', 'string', 'max:200'],
            'slides.*.button_text'  => ['nullable', 'string', 'max:40'],
            'slides.*.url'          => ['nullable', 'string', 'max:500'],
            'slides.*.sort_order'   => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);

        foreach ($request->input('slides', []) as $id => $fields) {
            $slide = HeroSliderImage::find((int) $id);
            if (!$slide) {
                continue;
            }
            $slide->update([
                'title'       => trim((string) ($fields['title'] ?? '')) ?: null,
                'subtitle'    => trim((string) ($fields['subtitle'] ?? '')) ?: null,
                'button_text' => trim((string) ($fields['button_text'] ?? '')) ?: null,
                'url'         => trim((string) ($fields['url'] ?? '')) ?: null,
                'sort_order'  => isset($fields['sort_order']) && $fields['sort_order'] !== '' ? (int) $fields['sort_order'] : $slide->sort_order,
            ]);
        }

        return redirect()->back()->with('details_success', 'Slide text and links saved.');
    }
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('delete_ids', []);

        if (empty($ids)) {
            return redirect()->back()->with('delete_info', 'No images were selected for deletion.');
        }

        $records = HeroSliderImage::whereIn('id', $ids)->get();

        foreach ($records as $record) {
            $path = $record->image ? public_path('assets/img/hero/' . $record->image) : null;
            if ($path && file_exists($path)) {
                @unlink($path);
            }
            $record->delete();
        }

        return redirect()->back()->with('delete_success', count($records) . ' slide(s) deleted.');
    }
}
