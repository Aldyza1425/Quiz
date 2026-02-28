<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\User;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        $categories = [
            ['name' => 'Matematika', 'description' => 'Quiz seputar matematika dasar, aljabar, dan geometri.'],
            ['name' => 'Bahasa Indonesia', 'description' => 'Quiz tata bahasa, sastra, dan pemahaman teks Bahasa Indonesia.'],
            ['name' => 'Bahasa Inggris', 'description' => 'Quiz kemampuan grammar, vocabulary, dan reading Bahasa Inggris.'],
            ['name' => 'Sains (IPA)', 'description' => 'Eksplorasi ilmu pengetahuan alam secara umum.'],
            ['name' => 'Biologi', 'description' => 'Quiz tentang makhluk hidup, genetika, dan ekosistem.'],
            ['name' => 'Fisika', 'description' => 'Quiz tentang energi, gaya, gerak, dan hukum alam.'],
            ['name' => 'Kimia', 'description' => 'Quiz tentang struktur materi, reasi kimia, dan unsur.'],
            ['name' => 'Sejarah', 'description' => 'Mengenal peristiwa penting sejarah nasional dan dunia.'],
            ['name' => 'Geografi', 'description' => 'Quiz tentang bentang alam, kependudukan, dan peta dunia.'],
            ['name' => 'Ekonomi', 'description' => 'Quiz tentang manajemen keuangan, pasar, dan sistem ekonomi.'],
            ['name' => 'Sosiologi', 'description' => 'Quiz tentang interaksi sosial dan struktur masyarakat.'],
            ['name' => 'Teknologi Informasi (TIK)', 'description' => 'Quiz seputar dunia digital, komputer, dan pemrograman.'],
            ['name' => 'Seni Budaya', 'description' => 'Quiz tentang seni rupa, musik, tari, dan teater.'],
            ['name' => 'Penjasorkes', 'description' => 'Quiz tentang kesehatan, olahraga, dan pola hidup sehat.'],
            ['name' => 'Agama', 'description' => 'Quiz pemahaman nilai-nilai spiritual dan keagamaan.'],
            ['name' => 'Pendidikan Pancasila (PPKn)', 'description' => 'Quiz tentang kewarganegaraan, hukum, dan ideologi negara.'],
            ['name' => 'Prakarya dan Kewirausahaan', 'description' => 'Quiz tentang kreativitas dan dasar-dasar bisnis.'],
            ['name' => 'Akuntansi', 'description' => 'Quiz tentang pencatatan keuangan dan siklus akuntansi.'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                [
                    'description' => $category['description'],
                    'created_by' => $admin->id,
                ]
            );
        }
    }
}
