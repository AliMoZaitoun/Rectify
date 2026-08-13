<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Complaint\Category;
use App\Models\Complaint\Complaint;
use App\Models\Complaint\ComplaintRating; // تأكد من المسار الصحيح لمودل التقييم
use App\Models\Core\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ComplaintSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::all();
        $branches = Branch::all();
        $categories = Category::all();

        if ($branches->isEmpty() || $categories->isEmpty() || $clients->isEmpty()) {
            return;
        }

        // توزيع الفروع
        $mainBranch = $branches->first();
        $secondBranch = $branches->count() > 1 ? $branches[1] : $mainBranch;

        // توزيع العملاء
        $client1 = $clients->first(); // عبد الله
        $client2 = $clients->last();  // مريم

        // توزيع الفئات
        $fastServiceCategory = $categories->where('sla_hours', 24)->first() ?? $categories->first();
        $equipmentCategory   = $categories->where('sla_hours', 6)->first() ?? $categories->first();
        $foodCategory        = $categories->where('sla_hours', 12)->first() ?? $categories->first();

        /*
        |--------------------------------------------------------------------------
        | 1. شكاوى متأخرة (تجاوزت الـ SLA) - لاختبار مؤشرات الأداء (SLA Breached)
        |--------------------------------------------------------------------------
        */

        // شكوى متأخرة جداً قيد الانتظار (Escalated)
        Complaint::create([
            'client_id'      => $client1->id,
            'device_id'      => 'device-uuid-client-1111',
            'uuid'           => (string) Str::uuid(),
            'branch_id'      => $mainBranch->id,
            'category_id'    => $equipmentCategory->id,
            'title'          => 'عطل في جهاز البلايستيشن - متأخرة (اختبار التصعيد)',
            'description'    => 'الجهاز يتوقف عن العمل أثناء اللعب، وتم تقديم الشكوى سابقاً ولم تُحل.',
            'priority'       => 'high',
            'is_anonymous'   => false,
            'tracking_code'  => 'CMP-OVERDUE01',
            'status'         => 'escalated',
            'created_at'     => now()->subHours(15),
            'sla_due_at'     => now()->subHours(9),  // تجاوزت الوقت بـ 9 ساعات
        ]);

        // شكوى متأخرة مجهولة قيد المعالجة (In Progress)
        Complaint::create([
            'client_id'      => null,
            'device_id'      => 'device-guest-test-9999',
            'uuid'           => (string) Str::uuid(),
            'branch_id'      => $secondBranch->id,
            'category_id'    => $foodCategory->id,
            'title'          => 'تأخير شديد في الطلب - متأخرة (اختبار التصعيد)',
            'description'    => 'تم الانتظار لأكثر من ساعة بدون استجابة من الموظفين.',
            'priority'       => 'urgent',
            'is_anonymous'   => true,
            'tracking_code'  => 'CMP-OVERDUE02',
            'status'         => 'in_progress',
            'created_at'     => now()->subHours(20),
            'sla_due_at'     => now()->subHours(8), // تجاوزت الوقت بـ 8 ساعات
        ]);

        /*
        |--------------------------------------------------------------------------
        | 2. شكاوى طبيعية قيد الانتظار والمعالجة (Within SLA)
        |--------------------------------------------------------------------------
        */

        Complaint::create([
            'client_id'      => $client2->id,
            'device_id'      => 'device-uuid-client-2222',
            'uuid'           => (string) Str::uuid(),
            'branch_id'      => $mainBranch->id,
            'category_id'    => $fastServiceCategory->id,
            'title'          => 'تأخير في تقديم الخدمة',
            'description'    => 'انتظرت أكثر من 30 دقيقة للحصول على الطلب في الفرع.',
            'priority'       => 'medium',
            'is_anonymous'   => false,
            'tracking_code'  => 'CMP-REG12345',
            'status'         => 'pending',
            'created_at'     => now()->subHours(2),
            'sla_due_at'     => now()->subHours(2)->addHours($fastServiceCategory->sla_hours),
        ]);

        Complaint::create([
            'client_id'      => $client1->id,
            'device_id'      => 'device-uuid-client-1111',
            'uuid'           => (string) Str::uuid(),
            'branch_id'      => $mainBranch->id,
            'category_id'    => $foodCategory->id,
            'title'          => 'شكوى مجهولة قيد المعالجة',
            'description'    => 'التعامل من الموظف كان غير لائق، أرجو المتابعة بدون كشف هويتي.',
            'priority'       => 'high',
            'is_anonymous'   => true,
            'tracking_code'  => 'CMP-ANON123',
            'status'         => 'in_progress',
            'created_at'     => now()->subHours(4),
            'sla_due_at'     => now()->subHours(4)->addHours($foodCategory->sla_hours),
        ]);

        // شكوى مرفوضة (Rejected)
        Complaint::create([
            'client_id'      => $client2->id,
            'device_id'      => 'device-uuid-client-2222',
            'uuid'           => (string) Str::uuid(),
            'branch_id'      => $secondBranch->id,
            'category_id'    => $equipmentCategory->id,
            'title'          => 'طلب غير منطقي',
            'description'    => 'العميلة تطلب استرداد مبلغ للعبة لعبتها منذ 3 أيام.',
            'priority'       => 'low',
            'is_anonymous'   => false,
            'tracking_code'  => 'CMP-REJ001',
            'status'         => 'rejected',
            'created_at'     => now()->subDays(2),
            'sla_due_at'     => now()->subDays(2)->addHours($equipmentCategory->sla_hours),
            'resolved_at'    => now()->subDays(1),
        ]);

        /*
        |--------------------------------------------------------------------------
        | 3. شكاوى تم حلها مع تقييمات (CSAT & Resolved) - لاختبار المخططات
        |--------------------------------------------------------------------------
        */

        // تم الحل ضمن الوقت + تقييم 5 نجوم
        $resolvedExcellent = Complaint::create([
            'client_id'      => $client1->id,
            'device_id'      => 'device-uuid-client-1111',
            'uuid'           => (string) Str::uuid(),
            'branch_id'      => $mainBranch->id,
            'category_id'    => $foodCategory->id,
            'title'          => 'جودة الطعام سيئة',
            'description'    => 'الطعام كان بارداً عند التقديم.',
            'priority'       => 'medium',
            'is_anonymous'   => false,
            'tracking_code'  => 'CMP-RES-001',
            'status'         => 'resolved',
            'created_at'     => now()->subDays(3),
            'sla_due_at'     => now()->subDays(3)->addHours($foodCategory->sla_hours),
            'resolved_at'    => now()->subDays(3)->addHours(2), // تم الحل بسرعة خلال ساعتين
        ]);

        ComplaintRating::create([
            'complaint_id' => $resolvedExcellent->id,
            'rating'       => 5,
            'comment'      => 'شكراً على سرعة الاستجابة والتعويض الجميل.',
            'created_at'   => now()->subDays(2),
        ]);

        // تم الحل ضمن الوقت + تقييم 4 نجوم
        $resolvedGood = Complaint::create([
            'client_id'      => $client2->id,
            'device_id'      => 'device-uuid-client-2222',
            'uuid'           => (string) Str::uuid(),
            'branch_id'      => $secondBranch->id,
            'category_id'    => $fastServiceCategory->id,
            'title'          => 'ازدحام وعدم تنظيم',
            'description'    => 'لا يوجد تنظيم للدور في الفرع.',
            'priority'       => 'low',
            'is_anonymous'   => false,
            'tracking_code'  => 'CMP-RES-002',
            'status'         => 'resolved',
            'created_at'     => now()->subDays(4),
            'sla_due_at'     => now()->subDays(4)->addHours($fastServiceCategory->sla_hours),
            'resolved_at'    => now()->subDays(4)->addHours(5), // تم الحل خلال 5 ساعات
        ]);

        ComplaintRating::create([
            'complaint_id' => $resolvedGood->id,
            'rating'       => 4,
            'comment'      => 'استجابة جيدة ولكن يرجى تحسين التنظيم مستقبلاً.',
            'created_at'   => now()->subDays(3),
        ]);

        // تم الحل بعد تجاوز الوقت + تقييم 2 نجمة (مجهول)
        $resolvedBad = Complaint::create([
            'client_id'      => null,
            'device_id'      => 'device-guest-test-7777',
            'uuid'           => (string) Str::uuid(),
            'branch_id'      => $mainBranch->id,
            'category_id'    => $equipmentCategory->id,
            'title'          => 'عطل متكرر في الشاشات',
            'description'    => 'الشاشة تطفئ كل ربع ساعة.',
            'priority'       => 'high',
            'is_anonymous'   => true,
            'tracking_code'  => 'CMP-RES-003',
            'status'         => 'resolved',
            'created_at'     => now()->subDays(5),
            'sla_due_at'     => now()->subDays(5)->addHours($equipmentCategory->sla_hours), // الحد الزمني 6 ساعات
            'resolved_at'    => now()->subDays(4), // تم الحل بعد 24 ساعة (تجاوز كبير)
        ]);

        ComplaintRating::create([
            'complaint_id' => $resolvedBad->id,
            'rating'       => 2,
            'comment'      => 'تأخرتم جداً في الرد، المشكلة انحلت ولكن بعد فوات الأوان.',
            'created_at'   => now()->subDays(3),
        ]);

        // تم الحل ضمن الوقت + تقييم 3 نجوم
        $resolvedAverage = Complaint::create([
            'client_id'      => $client1->id,
            'device_id'      => 'device-uuid-client-1111',
            'uuid'           => (string) Str::uuid(),
            'branch_id'      => $mainBranch->id,
            'category_id'    => $foodCategory->id,
            'title'          => 'نظافة الطاولات',
            'description'    => 'الطاولة لم تكن نظيفة عند الجلوس.',
            'priority'       => 'medium',
            'is_anonymous'   => false,
            'tracking_code'  => 'CMP-RES-004',
            'status'         => 'resolved',
            'created_at'     => now()->subDays(6),
            'sla_due_at'     => now()->subDays(6)->addHours($foodCategory->sla_hours),
            'resolved_at'    => now()->subDays(6)->addHours(2),
        ]);

        ComplaintRating::create([
            'complaint_id' => $resolvedAverage->id,
            'rating'       => 3,
            'comment'      => 'تم التنظيف بعد تقديم الشكوى، شكراً.',
            'created_at'   => now()->subDays(5),
        ]);
    }
}
