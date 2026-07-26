<?php

namespace Database\Seeders;

use App\Models\Complaint\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => ['ar' => 'جودة الأطعمة والمشروبات', 'en' => 'Food & Beverage Quality'],
                'description' => ['ar' => 'شكاوى متعلقة بالمذاق، النظافة، أو حرارة الوجبات والمشروبات', 'en' => 'Complaints related to taste, hygiene, or temperature of food and drinks'],
                'sla_hours' => 12,
            ],
            [
                'name' => ['ar' => 'سرعة الخدمة والانتظار', 'en' => 'Service Speed & Waiting Time'],
                'description' => ['ar' => 'تأخر تقديم الطلبات أو الازدحام غير المنظم', 'en' => 'Delays in order processing or unorganized waiting lines'],
                'sla_hours' => 24,
            ],
            [
                'name' => ['ar' => 'سلوك الموظفين والتعامل', 'en' => 'Staff Behavior & Attitude'],
                'description' => ['ar' => 'تعامل الموظفين، سوء التفاهم، أو عدم الاحترافية', 'en' => 'Staff behavior, misunderstandings, or lack of professionalism'],
                'sla_hours' => 12,
            ],
            [
                'name' => ['ar' => 'أعطال الألعاب والتجهيزات', 'en' => 'Games & Equipment Malfunctions'],
                'description' => ['ar' => 'أعطال طاولات البلياردو، أجهزة البلايستيشن، أو ألعاب الألعاب الإلكترونية للأطفال', 'en' => 'Issues with pool tables, PlayStation consoles, or arcade/kids equipment'],
                'sla_hours' => 6,
            ],
            [
                'name' => ['ar' => 'النظافة والمرافق العامة', 'en' => 'Cleanliness & Facilities'],
                'description' => ['ar' => 'نظافة الصالات، الجلسات، والمرافق الخدمية', 'en' => 'Cleanliness of halls, seating areas, and restrooms'],
                'sla_hours' => 8,
            ],
            [
                'name' => ['ar' => 'الفواتير والأسعار', 'en' => 'Billing & Pricing'],
                'description' => ['ar' => 'خطأ في حساب الفاتورة، النقاط، أو العروض', 'en' => 'Discrepancies in billing, points calculation, or promotional offers'],
                'sla_hours' => 24,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
