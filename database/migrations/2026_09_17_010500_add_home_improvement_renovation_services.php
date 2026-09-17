<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('languages') || !Schema::hasTable('service_categories') || !Schema::hasTable('services') || !Schema::hasTable('service_contents')) {
            return;
        }

        $now = now();
        $languages = DB::table('languages')->get(['id', 'code', 'is_default']);

        foreach ($languages as $language) {
            $category = DB::table('service_categories')
                ->where('language_id', $language->id)
                ->where(function ($query) {
                    $query->where('slug', 'home-improvements')
                        ->orWhere('name', 'Home Improvements');
                })
                ->first();

            if (!$category) {
                $serialNumber = ((int) DB::table('service_categories')
                    ->where('language_id', $language->id)
                    ->max('serial_number')) + 1;

                $categoryId = DB::table('service_categories')->insertGetId([
                    'name' => 'Home Improvements',
                    'icon' => 'fal fa-home',
                    'image' => null,
                    'slug' => 'home-improvements',
                    'language_id' => $language->id,
                    'serial_number' => $serialNumber,
                    'status' => 1,
                    'background_color' => '#eff9f1',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            } else {
                $categoryId = $category->id;
                DB::table('service_categories')->where('id', $categoryId)->update([
                    'name' => 'Home Improvements',
                    'slug' => 'home-improvements',
                    'status' => 1,
                    'updated_at' => $now,
                ]);
            }

            foreach ($this->services() as $serviceData) {
                $content = DB::table('service_contents')
                    ->where('language_id', $language->id)
                    ->where('slug', $serviceData['slug'])
                    ->first();

                if ($content) {
                    $serviceId = $content->service_id;
                    DB::table('services')->where('id', $serviceId)->update($this->servicePayload($serviceData, $now));
                } else {
                    $serviceId = DB::table('services')->insertGetId($this->servicePayload($serviceData, $now) + [
                        'created_at' => $now,
                    ]);
                }

                DB::table('service_contents')->updateOrInsert(
                    [
                        'service_id' => $serviceId,
                        'language_id' => $language->id,
                    ],
                    [
                        'category_id' => $categoryId,
                        'features' => implode("\n", $serviceData['features']),
                        'name' => $serviceData['name'],
                        'address' => 'Edinburgh, Livingston, Central Scotland',
                        'postcode' => '',
                        'description' => $serviceData['description'],
                        'slug' => $serviceData['slug'],
                        'meta_keyword' => $serviceData['meta_keyword'],
                        'meta_description' => $serviceData['meta_description'],
                        'updated_at' => $now,
                        'created_at' => $content->created_at ?? $now,
                    ]
                );
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('service_contents') || !Schema::hasTable('services')) {
            return;
        }

        $slugs = collect($this->services())->pluck('slug')->all();
        $serviceIds = DB::table('service_contents')->whereIn('slug', $slugs)->pluck('service_id')->unique()->all();

        DB::table('service_contents')->whereIn('slug', $slugs)->delete();
        DB::table('services')->whereIn('id', $serviceIds)->delete();

        if (Schema::hasTable('service_categories')) {
            $categoryIds = DB::table('service_categories')->where('slug', 'home-improvements')->pluck('id');
            foreach ($categoryIds as $categoryId) {
                $hasServices = DB::table('service_contents')->where('category_id', $categoryId)->exists();
                if (!$hasServices) {
                    DB::table('service_categories')->where('id', $categoryId)->delete();
                }
            }
        }
    }

    private function servicePayload(array $serviceData, $now): array
    {
        return [
            'vendor_id' => 0,
            'staff_id' => null,
            'status' => 1,
            'service_type' => 'quote',
            'price' => 0,
            'prev_price' => null,
            'price_placeholder' => 'Free survey',
            'show_in_renewal_section' => 0,
            'renewal_section_sort_order' => 0,
            'offer_price' => null,
            'offer_label' => null,
            'renewal_short_description' => null,
            'service_image' => $serviceData['image'],
            'zoom_meeting' => 0,
            'calendar_status' => 0,
            'max_person' => null,
            'average_rating' => 0,
            'latitude' => null,
            'longitude' => null,
            'service_area_postcode' => null,
            'approval_status' => 'approved',
            'service_rejection_reason' => null,
            'updated_at' => $now,
        ];
    }

    private function services(): array
    {
        return [
            [
                'slug' => 'bathroom-renovation',
                'name' => 'Bathroom Renovation',
                'image' => 'bathroom-renovation.jpg',
                'description' => 'Complete bathroom design and installation managed by one trusted local team. Our local team keeps the work clear, respectful and planned around your property.',
                'meta_keyword' => 'bathroom renovation Edinburgh, bathroom renovation Livingston, bathroom installation Central Scotland, home improvements bathroom',
                'meta_description' => 'Bathroom Renovation in Edinburgh, Livingston and Central Scotland. Complete bathroom design and installation managed by one trusted local team.',
                'features' => [
                    'Free consultation',
                    'Bathroom design and layout review',
                    'Clear written quotation',
                    'Materials discussed in advance',
                    'Plumbing, fitting and installation coordination',
                    'Qualified workmanship',
                    'Progress communication',
                    'Clean and tidy handover',
                ],
            ],
            [
                'slug' => 'kitchen-renovation',
                'name' => 'Kitchen Renovation',
                'image' => 'kitchen-renovation.jpg',
                'description' => 'Practical, beautiful kitchens planned and installed around your home. Our local team keeps the work clear, respectful and planned around your property.',
                'meta_keyword' => 'kitchen renovation Edinburgh, kitchen renovation Livingston, kitchen installation Central Scotland, home improvements kitchen',
                'meta_description' => 'Kitchen Renovation in Edinburgh, Livingston and Central Scotland. Practical, beautiful kitchens planned and installed around your home.',
                'features' => [
                    'Free consultation',
                    'Kitchen layout and requirements review',
                    'Clear written quotation',
                    'Materials discussed in advance',
                    'Installation planning and coordination',
                    'Qualified workmanship',
                    'Progress communication',
                    'Clean and tidy handover',
                ],
            ],
        ];
    }
};