<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->truncate();
        $user = User::query()->create([
            'name' => 'Admin',
            'password' => bcrypt('5xM0I73Em5gN'),
            'email' => 'app@gmail.com',
        ]);
    }
}
