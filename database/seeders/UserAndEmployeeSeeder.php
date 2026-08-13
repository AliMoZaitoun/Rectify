<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Core\Branch;
use App\Models\Core\Employee;
use App\Models\Core\EmployeeBranch;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UserAndEmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $defaultLocationId = Location::first()?->id ?? 1;
        $branches = Branch::all();
        $faker = Faker::create('ar_SA'); // لتوليد أسماء عربية

        if ($branches->isEmpty()) {
            return;
        }

        // --- الموظفون والمدراء (كما هم في الكود الأساسي) ---
        $managersList = [
            ['first_name' => 'أحمد', 'last_name' => 'العلي'],
            ['first_name' => 'خالد', 'last_name' => 'العمر'],
            ['first_name' => 'طارق', 'last_name' => 'السيد'],
            ['first_name' => 'محمود', 'last_name' => 'الراشد'],
            ['first_name' => 'ماهر', 'last_name' => 'الحكيم'],
        ];

        $staffList = [
            [
                ['first_name' => 'سامر', 'last_name' => 'المصري', 'gender' => 'male'],
                ['first_name' => 'رنا', 'last_name' => 'الحسن', 'gender' => 'female'],
                ['first_name' => 'عمر', 'last_name' => 'النابلسي', 'gender' => 'male'],
            ],
            [
                ['first_name' => 'زياد', 'last_name' => 'الشامي', 'gender' => 'male'],
                ['first_name' => 'نور', 'last_name' => 'الخطيب', 'gender' => 'female'],
                ['first_name' => 'باسم', 'last_name' => 'الحداد', 'gender' => 'male'],
            ],
            [
                ['first_name' => 'كريم', 'last_name' => 'الفارس', 'gender' => 'male'],
                ['first_name' => 'سارة', 'last_name' => 'النجار', 'gender' => 'female'],
                ['first_name' => 'حسام', 'last_name' => 'الصالح', 'gender' => 'male'],
            ],
        ];

        foreach ($branches as $bIndex => $branch) {
            $managerName = $managersList[$bIndex % count($managersList)];

            $managerUser = User::create([
                'first_name'        => $managerName['first_name'],
                'last_name'         => $managerName['last_name'],
                'email'             => "manager.branch{$branch->id}@company.com",
                'phone'             => '091' . str_pad((string)$branch->id, 7, '0', STR_PAD_LEFT),
                'location_id'       => $defaultLocationId,
                'gender'            => 'male',
                'type'              => 'employee',
                'password'          => bcrypt('password'),
                'email_verified_at' => now(),
            ]);

            $managerEmployee = Employee::create(['user_id' => $managerUser->id]);
            $branch->update(['manager_id' => $managerEmployee->id]);

            EmployeeBranch::create([
                'employee_id' => $managerEmployee->id,
                'branch_id'   => $branch->id,
                'from_date'   => now()->subMonths(6)->toDateString(),
                'to_date'     => null,
                'position'    => 'manager',
            ]);

            $managerUser->assignRole('manager');
            $currentBranchStaff = $staffList[$bIndex % count($staffList)];

            foreach ($currentBranchStaff as $sIndex => $staffData) {
                $staffNumber = $sIndex + 1;
                $staffUser = User::create([
                    'first_name'        => $staffData['first_name'],
                    'last_name'         => $staffData['last_name'],
                    'email'             => "staff{$staffNumber}.branch{$branch->id}@company.com",
                    'phone'             => "092{$branch->id}0000{$staffNumber}",
                    'location_id'       => $defaultLocationId,
                    'gender'            => $staffData['gender'],
                    'type'              => 'employee',
                    'password'          => bcrypt('password'),
                    'email_verified_at' => now(),
                ]);

                $staffEmployee = Employee::create(['user_id' => $staffUser->id]);

                EmployeeBranch::create([
                    'employee_id' => $staffEmployee->id,
                    'branch_id'   => $branch->id,
                    'from_date'   => now()->subMonths(3)->toDateString(),
                    'to_date'     => null,
                    'position'    => 'staff',
                ]);

                $staffUser->assignRole('staff');
            }
        }

        for ($i = 1; $i <= 20; $i++) {
            $gender = $faker->randomElement(['male', 'female']);
            $clientUser = User::create([
                'first_name'        => $faker->firstName($gender),
                'last_name'         => $faker->lastName,
                'email'             => "client{$i}@gmail.com",
                'phone'             => $faker->unique()->numerify('095#######'),
                'location_id'       => $defaultLocationId,
                'gender'            => $gender,
                'type'              => 'client',
                'password'          => bcrypt('password'),
                'email_verified_at' => now(),
            ]);

            Client::create([
                'user_id' => $clientUser->id,
                'points'  => $faker->numberBetween(50, 1000),
            ]);

            $clientUser->assignRole(['client']);
        }
    }
}
