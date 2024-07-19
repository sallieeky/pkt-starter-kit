<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate([
            'username' => 'superadmin'
        ],[
            'name' => 'Superadmin',
            'npk' => 'superadmin',
            'email' => 'superadmin@app.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('Superadmin');
    }
}
