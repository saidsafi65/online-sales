<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    
    /**
     * Run the database seeds.
     */
    public function run()
    {
       User::create([
        'name' => 'online_sale',
        'email' => 'onlinesales0597848937@gmail.com',
        'password' => Hash::make('Pass@1234'),
        ]);

          // Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('Admin@123'),
            'branch_id' => 1, // ممكن يكون أي فرع، لأن ال Admin يشوف كل شيء
            'role' => 'admin',
            'status' => 'active',
        ]);

        // موظف فرع خانيونس
        User::create([
            'name' => 'KhanYounis',
            'email' => 'KhanYounis@onlinesales0597848937.com',
            'password' => Hash::make('Pass@1234'),
            'branch_id' => 1,
            'role' => 'employee',
            'status' => 'active',
        ]);

        // مدير فرع الدير
        User::create([
            'name' => 'Manager Deir',
            'email' => 'manager.deir@example.com',
            'password' => Hash::make('Manager@123'),
            'branch_id' => 2,
            'role' => 'manager',
            'status' => 'active',
        ]);

        // موظف فرع رفح
        User::create([
            'name' => 'Employee Rafah',
            'email' => 'employee.rafah@example.com',
            'password' => Hash::make('Employee@123'),
            'branch_id' => 3,
            'role' => 'employee',
            'status' => 'inactive', // مثال على مستخدم غير مفعل
        ]);
    //     $this->call([
    //     BranchSeeder::class, // لو عندك فروع
    //     UserSeeder::class,
    // ]);
    }
}
