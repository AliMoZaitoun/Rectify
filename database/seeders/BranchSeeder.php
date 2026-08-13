<?php

namespace Database\Seeders;

use App\Models\Core\Branch;
use App\Models\Location;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $locations = Location::where('type', 'subdistrict')->pluck('id')->toArray();
        $defaultLocationId = $locations[0] ?? Location::first()?->id ?? 1;

        $branches = [
            [
                'name' => ['ar' => 'المطعم الرئيسي', 'en' => 'Main Restaurant'],
                'description' => ['ar' => 'قسم وجبات الطعام والأطباق الرئيسية', 'en' => 'Main dining and dish menu section'],
                'location_id' => $locations[0] ?? $defaultLocationId,
                'manager_id'  => null,
                'monthly_amount_budget' => 2000.00,
                'monthly_points_budget' => 10000,
            ],
            [
                'name' => ['ar' => 'الكافيه والقهوة المختصة', 'en' => 'Specialty Cafe'],
                'description' => ['ar' => 'قسم المشروبات الساخنة، الباردة والحلويات', 'en' => 'Hot & cold beverages and desserts section'],
                'location_id' => $locations[1] ?? $defaultLocationId,
                'manager_id'  => null,
                'monthly_amount_budget' => 1500.00,
                'monthly_points_budget' => 8000,
            ],
            [
                'name' => ['ar' => 'غرف ألعاب الفوز (VR & Virtual)', 'en' => 'Victory Game Rooms'],
                'description' => ['ar' => 'غرف المحاكاة وألعاب الواقع الافتراضي', 'en' => 'Virtual reality and simulator game rooms'],
                'location_id' => $locations[2] ?? $defaultLocationId,
                'manager_id'  => null,
                'monthly_amount_budget' => 3000.00,
                'monthly_points_budget' => 15000,
            ],
            [
                'name' => ['ar' => 'أكشاك الطعام السريع', 'en' => 'Food Kiosks'],
                'description' => ['ar' => 'أكشاك المأكولات الخفيفة والسريعة بالمساحات المفتوحة', 'en' => 'Express snacks and fast food booths'],
                'location_id' => $locations[3] ?? $defaultLocationId,
                'manager_id'  => null,
                'monthly_amount_budget' => 1000.00,
                'monthly_points_budget' => 5000,
            ],
            [
                'name' => ['ar' => 'صالة البلياردو والسنوكر', 'en' => 'Billiards & Snooker Hall'],
                'description' => ['ar' => 'صالة طاولات البلياردو والتحديات', 'en' => 'Billiards and snooker tournament area'],
                'location_id' => $locations[0] ?? $defaultLocationId,
                'manager_id'  => null,
                'monthly_amount_budget' => 1000.00,
                'monthly_points_budget' => 5000,
            ],
            [
                'name' => ['ar' => 'صالة البلايستيشن والـ Gaming', 'en' => 'PlayStation & Gaming Zone'],
                'description' => ['ar' => 'شاشات العرض الحديثة وأحدث أجهزة الألعاب', 'en' => 'High-end gaming zone with latest consoles'],
                'location_id' => $locations[1] ?? $defaultLocationId,
                'manager_id'  => null,
                'monthly_amount_budget' => 2500.00,
                'monthly_points_budget' => 12000,
            ],
            [
                'name' => ['ar' => 'منطقة ألعاب الأطفال (Kids Zone)', 'en' => 'Kids Play Zone'],
                'description' => ['ar' => 'منطقة الألعاب الآمنة والمجسمات الترفيهية للأطفال', 'en' => 'Safe play area and fun activities for kids'],
                'location_id' => $locations[2] ?? $defaultLocationId,
                'manager_id'  => null,
                'monthly_amount_budget' => 1500.00,
                'monthly_points_budget' => 8000,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
