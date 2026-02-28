<?php

namespace Database\Seeders;

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
        // 3 Admin
        $admins = [
            ['name' => 'Admin Utama', 'email' => 'admin@sekolah.id', 'role' => 'admin'],
            ['name' => 'Admin Sekunder', 'email' => 'admin2@sekolah.id', 'role' => 'admin'],
            ['name' => 'Staff IT', 'email' => 'staff-it@sekolah.id', 'role' => 'admin'],
        ];

        foreach ($admins as $admin) {
            User::create([
                'name' => $admin['name'],
                'email' => $admin['email'],
                'password' => Hash::make('password'),
                'role' => $admin['role'],
            ]);
        }

        // 5 Guru
        $teachers = [
            'Budi Santoso',
            'Siti Aminah',
            'Ahmad Dani',
            'Dewi Lestari',
            'Joko Susilo'
        ];

        foreach ($teachers as $idx => $name) {
            User::create([
                'name' => "Guru $name",
                'email' => "guru" . ($idx + 1) . "@sekolah.id",
                'password' => Hash::make('password'),
                'role' => 'guru',
            ]);
        }

        // 20 Siswa
        for ($i = 1; $i <= 20; $i++) {
            User::create([
                'name' => "Siswa Teladan $i",
                'email' => "siswa$i@sekolah.id",
                'password' => Hash::make('password'),
                'role' => 'siswa',
            ]);
        }
    }
}
