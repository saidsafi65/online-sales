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
    public function run(): void
    {
       User::create([
        'name' => 'online_sale',
        'email' => 'onlinesales0597848937@gmail.com',
        'password' => Hash::make('Pass@1234'),
        ]);
    }
}
