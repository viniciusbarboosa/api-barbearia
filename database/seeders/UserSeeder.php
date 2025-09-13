<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->count(20)->create([
            'user_type' => 'B',
            'approved' => true,
        ]);

        User::factory()->count(20)->create([
            'user_type' => 'U',
            'approved' => true,
        ]);
    }
}
