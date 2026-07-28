<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Complaint\Category;
use App\Models\Complaint\Complaint;
use App\Models\Core\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ComplaintSeeder extends Seeder
{
    public function run(): void
    {
        $client = Client::first();
        $branch = Branch::first();
        $category = Category::first();

        if (!$branch || !$category) {
            return;
        }

        Complaint::create([
            'client_id'      => $client?->id,
            'device_id'      => 'device-uuid-client-1111',
            'branch_id'      => $branch->id,
            'category_id'    => $category->id,
            'title'          => 'تأخير في تقديم الخدمة',
            'description'    => 'انتظرت أكثر من 30 دقيقة للحصول على الطلب في الفرع.',
            'priority'       => 'medium',
            'is_anonymous'   => false,
            'tracking_code'  => 'CMP-REG12345',
            'status'         => 'pending',
            'sla_due_at'     => now()->addHours(24),
        ]);

        Complaint::create([
            'client_id'      => $client?->id,
            'device_id'      => 'device-uuid-client-1111',
            'branch_id'      => $branch->id,
            'category_id'    => $category->id,
            'title'          => 'شكوى مجهولة من عميل مسجل',
            'description'    => 'التعامل من الموظف كان غير لائق، أرجو المتابعة بدون كشف هويتي.',
            'priority'       => 'high',
            'is_anonymous'   => true,
            'tracking_code'  => 'CMP-ANON123',
            'status'         => 'pending',
            'sla_due_at'     => now()->addHours(24),
        ]);

        Complaint::create([
            'client_id'      => null,
            'device_id'      => 'device-guest-test-9999',
            'branch_id'      => $branch->id,
            'category_id'    => $category->id,
            'title'          => 'شكوى من زائر مجهول',
            'description'    => 'جربت التطبيق كـ ضيف والخدمة كانت تحتاج تحسين.',
            'priority'       => 'low',
            'is_anonymous'   => true,
            'tracking_code'  => 'CMP-GUEST99',
            'status'         => 'pending',
            'sla_due_at'     => now()->addHours(24),
        ]);
    }
}
