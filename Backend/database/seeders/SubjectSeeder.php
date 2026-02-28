<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            ['name' => 'Matematika', 'code' => 'MTK'],
            ['name' => 'Fisika', 'code' => 'FIS'],
            ['name' => 'Biologi', 'code' => 'BIO'],
            ['name' => 'Kimia', 'code' => 'KIM'],
            ['name' => 'Bahasa Indonesia', 'code' => 'BIND'],
            ['name' => 'Bahasa Inggris', 'code' => 'BING'],
            ['name' => 'Sejarah', 'code' => 'SEJ'],
            ['name' => 'Ekonomi', 'code' => 'EKO'],
            ['name' => 'Geografi', 'code' => 'GEO'],
            ['name' => 'Sosiologi', 'code' => 'SOS'],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}
