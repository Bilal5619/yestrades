<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('service_categories') || !Schema::hasTable('languages')) {
            return;
        }

        $languages = DB::table('languages')->select('id')->get();
        $hasTimestamps = Schema::hasColumn('service_categories', 'created_at')
            && Schema::hasColumn('service_categories', 'updated_at');

        foreach ($languages as $language) {
            $existingCategory = DB::table('service_categories')
                ->where('language_id', $language->id)
                ->whereRaw('LOWER(name) = ?', ['handy man service'])
                ->first();

            if ($existingCategory) {
                $updates = [
                    'name' => 'Handy man service',
                    'slug' => Str::slug('Handy man service'),
                    'status' => 1,
                ];

                if (Schema::hasColumn('service_categories', 'updated_at')) {
                    $updates['updated_at'] = now();
                }

                DB::table('service_categories')->where('id', $existingCategory->id)->update($updates);

                continue;
            }

            $serialNumber = (int) DB::table('service_categories')
                ->where('language_id', $language->id)
                ->max('serial_number');

            $category = [
                'name' => 'Handy man service',
                'slug' => Str::slug('Handy man service'),
                'language_id' => $language->id,
                'serial_number' => $serialNumber + 1,
                'status' => 1,
            ];

            foreach (['icon', 'image', 'background_color'] as $column) {
                if (Schema::hasColumn('service_categories', $column)) {
                    $category[$column] = null;
                }
            }

            if ($hasTimestamps) {
                $category['created_at'] = now();
                $category['updated_at'] = now();
            }

            DB::table('service_categories')->insert($category);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('service_categories')) {
            return;
        }

        DB::table('service_categories')
            ->whereRaw('LOWER(name) = ?', ['handy man service'])
            ->delete();
    }
};
