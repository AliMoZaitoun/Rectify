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

        $b1 = $branches->first();
        $b2 = $branches->count() > 1 ? $branches[1] : $b1;

        $c1 = $clients->first();
        $c2 = $clients->count() > 1 ? $clients[1] : $c1;
        $c3 = $clients->count() > 2 ? $clients[2] : $c1;

        $staff = Employee::whereHas('user.roles', function ($q) {
            $q->where('name', 'staff');
        })->first() ?? Employee::first();

        $manager = Employee::whereHas('user.roles', function ($q) {
            $q->where('name', 'manager');
        })->first() ?? Employee::first();

        $catFood = $categories->where('sla_hours', 12)->first() ?? $categories->first();
        $catSpeed = $categories->where('sla_hours', 24)->first() ?? $categories->first();
        $catEquip = $categories->where('sla_hours', 6)->first() ?? $categories->first();

        Complaint::create([
            'client_id'      => $c1->id,
            'branch_id'      => $b1->id,
            'category_id'    => $catFood->id,
            'uuid'           => (string) Str::uuid(),
            'device_id'      => 'dev-' . Str::random(6),
            'title'          => 'Food issue',
            'description'    => 'Food was delivered cold.',
            'priority'       => 'medium',
            'is_anonymous'   => false,
            'tracking_code'  => 'CMP-' . strtoupper(Str::random(6)),
            'status'         => 'pending',
            'created_at'     => Carbon::now()->subHours(2),
            'sla_due_at'     => Carbon::now()->subHours(2)->addHours($catFood->sla_hours),
        ]);

        $compStaffExceed = Complaint::create([
            'client_id'      => $c2->id,
            'branch_id'      => $staff->branch_id ?? $b1->id,
            'category_id'    => $catSpeed->id,
            'uuid'           => (string) Str::uuid(),
            'device_id'      => 'dev-' . Str::random(6),
            'title'          => 'Very late service',
            'description'    => 'Waited 1 hour for my order.',
            'priority'       => 'high',
            'is_anonymous'   => false,
            'tracking_code'  => 'CMP-' . strtoupper(Str::random(6)),
            'status'         => 'resolved',
            'created_at'     => Carbon::now()->subDays(2),
            'sla_due_at'     => Carbon::now()->subDays(2)->addHours($catSpeed->sla_hours),
            'resolved_at'    => Carbon::now()->subDays(1),
        ]);

        ComplaintCompensation::create([
            'complaint_id'   => $compStaffExceed->id,
            'branch_id'      => $compStaffExceed->branch_id,
            'client_id'      => $c2->id,
            'approved_by_id' => $staff->id,
            'type'           => 'points',
            'amount'         => 120.00,
            'notes'          => 'Staff requesting 120 points. Limit is 50. Needs manager approval.',
            'status'         => 'pending_approval',
            'granted_at'     => null,
        ]);

        $compManagerExceed = Complaint::create([
            'client_id'      => $c3->id,
            'branch_id'      => $manager->branch_id ?? $b2->id,
            'category_id'    => $catEquip->id,
            'uuid'           => (string) Str::uuid(),
            'device_id'      => 'dev-' . Str::random(6),
            'title'          => 'Broken equipment caused injury',
            'description'    => 'Customer fell due to broken chair.',
            'priority'       => 'urgent',
            'is_anonymous'   => false,
            'tracking_code'  => 'CMP-' . strtoupper(Str::random(6)),
            'status'         => 'resolved',
            'created_at'     => Carbon::now()->subDays(3),
            'sla_due_at'     => Carbon::now()->subDays(3)->addHours($catEquip->sla_hours),
            'resolved_at'    => Carbon::now()->subDays(2),
        ]);

        ComplaintCompensation::create([
            'complaint_id'   => $compManagerExceed->id,
            'branch_id'      => $compManagerExceed->branch_id,
            'client_id'      => $c3->id,
            'approved_by_id' => $manager->id,
            'type'           => 'coupon',
            'amount'         => 500.00,
            'notes'          => 'Manager requesting 500 value coupon. Limit is 200. Needs admin approval.',
            'status'         => 'pending_approval',
            'granted_at'     => null,
        ]);

        $compNormal = Complaint::create([
            'client_id'      => $c1->id,
            'branch_id'      => $b1->id,
            'category_id'    => $catSpeed->id,
            'uuid'           => (string) Str::uuid(),
            'device_id'      => 'dev-' . Str::random(6),
            'title'          => 'Minor delay',
            'description'    => 'Order delayed by 10 minutes.',
            'priority'       => 'low',
            'is_anonymous'   => false,
            'tracking_code'  => 'CMP-' . strtoupper(Str::random(6)),
            'status'         => 'resolved',
            'created_at'     => Carbon::now()->subDays(5),
            'sla_due_at'     => Carbon::now()->subDays(5)->addHours($catSpeed->sla_hours),
            'resolved_at'    => Carbon::now()->subDays(5)->addHours(2),
        ]);

        ComplaintRating::create([
            'complaint_id' => $compNormal->id,
            'rating'       => 4,
            'comment'      => 'Good response time.',
        ]);

        ComplaintCompensation::create([
            'complaint_id'   => $compNormal->id,
            'branch_id'      => $b1->id,
            'client_id'      => $c1->id,
            'approved_by_id' => $staff->id,
            'type'           => 'points',
            'amount'         => 30.00,
            'notes'          => 'Normal compensation within staff limit.',
            'status'         => 'granted',
            'granted_at'     => Carbon::now()->subDays(5)->addHours(2),
        ]);
    }
}
