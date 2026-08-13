<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Core\Employee;
use App\Models\Complaint\Category;
use App\Models\Complaint\Complaint;
use App\Models\Complaint\ComplaintRating;
use App\Models\Complaint\ComplaintCompensation;
use App\Models\Core\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Faker\Factory as Faker;

class ComplaintSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::all();
        $branches = Branch::all();
        $categories = Category::all();
        $faker = Faker::create('ar_SA');

        if ($branches->isEmpty() || $categories->isEmpty() || $clients->isEmpty()) {
            return;
        }

        $staff = Employee::whereHas('user.roles', function ($q) {
            $q->where('name', 'staff');
        })->first() ?? Employee::first();

        $manager = Employee::whereHas('user.roles', function ($q) {
            $q->where('name', 'manager');
        })->first() ?? Employee::first();

        // بنك شكاوى حقيقي وواقعي وواضح للتقارير واللقطات الشاشة (Screenshots)
        $realComplaintBank = [
            'جودة الأطعمة والمشروبات' => [
                ['title' => 'البرجر وصل بارد جداً وغير ساخن', 'description' => 'طلبت وجبة دبل برجر ولكن الخبز كان بارد والجبنة غير ذائبة تماماً، نرجو الاهتمام بجودة المطبخ وسرعة التقديم.'],
                ['title' => 'تأخر المشروبات الباردة عن الوجبة', 'description' => 'انتظرنا عصائر المانجو أكثر من 35 دقيقة رغم أن الصالة لم تكن مزدحمة نهائياً وقت الطلب.'],
                ['title' => 'طعم غريب وحامض في المشروب الغازي', 'description' => 'يبدو أن ماكينة السوفت درينك تحتاج لتنظيف فوري، طعم الكولا كان فيه حموضة غير مقبولة.'],
                ['title' => 'الوجبة ناقصة طلبات جانبية', 'description' => 'وصلت طلبات الطاولة الرئيسية ولكن تم نسيان البطاطس المقلي والصلصات الخاصة بالوجبة.'],
            ],
            'سرعة الخدمة والانتظار' => [
                ['title' => 'طابور طويل وعدم تنظيم في الأدوار', 'description' => 'وقفنا طويلاً عند الكاشير ولا يوجد تنظيم واضح للأدوار، وبعض الزبائن تجاوزونا بالدور وسط تجاهل الموظفين.'],
                ['title' => 'تأخر استلام الطاولة رغم الحجز المسبق', 'description' => 'كان لدينا حجز مسبق لطاولة عائلية واضطررنا للانتظار لأكثر من ربع ساعة إضافية حتى تم تجهيزها.'],
                ['title' => 'الويتر بطيء جداً بالاستجابة لطلبات الطاولة', 'description' => 'نادينا الموظف أكثر من مرتين لتعديل الطلب وإحضار الماء ولكن لم يحضر أحد بسرعة مناسبة.'],
            ],
            'سلوك الموظفين والتعامل' => [
                ['title' => 'تعامل غير لائق من موظف الاستقبال على البوابة', 'description' => 'الموظف عند البوابة كان أسلوبه جافاً جداً في التحدث وتجاهل استفسارنا عن العروض المتاحة.'],
                ['title' => 'عدم احترافية في التعامل مع الشكاوى', 'description' => 'أثناء تنبيه الكاشير لوجود خطأ بالفاتورة، تعامل بحدة ورفض الاستماع لتوضيح المشكلة بهدوء.'],
            ],
            'أعطال الألعاب والتجهيزات' => [
                ['title' => 'عطل تكنولوجي في ذراع جهاز بلايستيشن 5', 'description' => 'الجهاز رقم 4 في صالة الألعاب ذراع التحكم فيه يعاني من مشاكل قطعية في الأزرار وتأخر كبير بالاستجابة.'],
                ['title' => 'طاولة البلياردو مائلة وعصي اللعب تالفة', 'description' => 'طاولة البلياردو رقم 2 غير مستوية تماماً، وعصي اللعب بدون رؤوس جلدية صالحة للعب الاحترافي.'],
                ['title' => 'تقطيع مستمر في شاشة العرض الرئيسية', 'description' => 'شاشة البث في الصالة الكبرى كانت تفصل كل بضع دقائق وتظهر شاشة سوداء أثناء المباريات.'],
            ],
            'النظافة والمرافق العامة' => [
                ['title' => 'نظافة دورات المياه سيئة وتحتاج عناية', 'description' => 'دورة المياه بحاجة لعناية وعمال نظافة بشكل دوري، كانت الأرضيات مبللة ومهملة تماماً.'],
                ['title' => 'بقايا طعام على الجلسات والأرضية لم تنظف', 'description' => 'عند جلوسنا على الطاولة وجدنا بقايا طعام من الزبون السابق ولم يتم مسحها وتعقيمها بشكل جيد.'],
            ],
            'الفواتير والأسعار' => [
                ['title' => 'خطأ في احتساب الخصم أو العرض المعلن', 'description' => 'تمت محاسبتنا بسعر القائمة الكاملة رغم أننا استخدمنا كوبون العرض الأسبوعي المعلن بالمركِز.'],
                ['title' => 'نقاط الولاء والمكافآت لم يتم إضافتها', 'description' => 'دفعت الفاتورة برقم الجوّال ولم يتم احتساب النقاط الخاصة ببرنامج المكافآت برصيد حسابي.'],
            ],
        ];

        foreach ($clients as $index => $client) {
            $branch = $branches->random();
            $category = $categories->random();

            $categoryNameAr = is_array($category->name) ? ($category->name['ar'] ?? '') : $category->name;
            $bankOptions = $realComplaintBank[$categoryNameAr] ?? [
                ['title' => 'ملاحظة عامة على مستوى الخدمة', 'description' => 'نرجو تحسين مستوى الخدمة العامة في الفرع لتقديم تجربة ترفيهية أفضل للعملاء.']
            ];

            // تحديد عدد الشكاوى وسيناريوهات التسرب والتعويضات
            if ($index < 5) {
                $complaintCount = 4;
                $hasRejectedCompensation = false;
            } elseif ($index >= 5 && $index < 10) {
                $complaintCount = 3;
                $hasRejectedCompensation = true; // تعويض مرفوض للعملاء الحرجين
            } else {
                $complaintCount = rand(1, 3);
                $hasRejectedCompensation = false;
            }

            for ($i = 0; $i < $complaintCount; $i++) {
                $status = $faker->randomElement(['pending', 'in_progress', 'resolved', 'closed']);
                $createdAt = Carbon::now()->subDays(rand(1, 28));

                $selectedComplaint = $faker->randomElement($bankOptions);

                $complaint = Complaint::create([
                    'client_id'      => $client->id,
                    'branch_id'      => $branch->id,
                    'category_id'    => $category->id,
                    'uuid'           => (string) Str::uuid(),
                    'device_id'      => 'dev-' . Str::random(6),
                    'title'          => $selectedComplaint['title'],
                    'description'    => $selectedComplaint['description'],
                    'priority'       => $faker->randomElement(['low', 'medium', 'high', 'urgent']),
                    'is_anonymous'   => false,
                    'tracking_code'  => 'CMP-' . strtoupper(Str::random(6)),
                    'status'         => 'pending',
                    'created_at'     => $createdAt,
                    'sla_due_at'     => $createdAt->copy()->addHours($category->sla_hours),
                ]);

                if ($status !== 'pending') {
                    $complaint->update([
                        'status'      => $status,
                        'resolved_at' => in_array($status, ['resolved', 'closed']) ? $createdAt->copy()->addHours(rand(2, 24)) : null,
                    ]);
                }

                // 🛑 القاعدة الصارمة: التعويض يُنشأ فقط حصراً إذا كانت الشكوى محلولة (resolved أو closed)
                $isResolvedOrClosed = in_array($status, ['resolved', 'closed']);

                if ($isResolvedOrClosed) {
                    if ($hasRejectedCompensation && $i === 0) {
                        // تعويض مرفوض لشكوى محلولة تجاوزت الشروط
                        ComplaintCompensation::create([
                            'complaint_id'   => $complaint->id,
                            'branch_id'      => $branch->id,
                            'client_id'      => $client->id,
                            'approved_by_id' => $manager->id,
                            'type'           => 'coupon',
                            'amount'         => 500.00,
                            'notes'          => 'مرفوض بسبب تجاوز الحد المسموح به للتعويضات الإضافية هذا الشهر.',
                            'status'         => 'rejected',
                            'granted_at'     => null,
                            'created_at'     => $createdAt->copy()->addHours(2),
                        ]);
                    } elseif (rand(1, 10) > 5) {
                        // تعويض مقبول ومنح بنجاح لشكوى محلولة
                        ComplaintCompensation::create([
                            'complaint_id'   => $complaint->id,
                            'branch_id'      => $branch->id,
                            'client_id'      => $client->id,
                            'approved_by_id' => $staff->id,
                            'type'           => 'points',
                            'amount'         => $faker->randomElement([50, 100, 150, 200]),
                            'notes'          => 'تم إرضاء العميل وتعويضه بنقاط ولاء إضافية نظير التأخير.',
                            'status'         => 'granted',
                            'granted_at'     => $createdAt->copy()->addHours(3),
                            'created_at'     => $createdAt->copy()->addHours(2),
                        ]);
                    }
                }

                // إضافة تقييمات واقعية للشكاوى المحلولة أو المغلقة فقط
                if ($isResolvedOrClosed && rand(1, 10) > 4) {
                    ComplaintRating::create([
                        'complaint_id' => $complaint->id,
                        'rating'       => rand(3, 5),
                        'comment'      => $faker->randomElement([
                            'شكراً لتجاوبكم السريع وحل المشكلة باحترافية عالية.',
                            'تم تدارك الأمر وإرضائنا بالتعويض المناسب، يعطيكم العافية.',
                            'خدمة العملاء ممتازة وسريعة الاستجابة في المتابعة.',
                            'أشكر الإدارة على الاهتمام وحل الشكوى حتى النهاية.'
                        ]),
                    ]);
                }
            }
        }
    }
}
