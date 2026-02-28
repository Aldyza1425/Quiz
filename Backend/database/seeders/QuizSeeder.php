<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quiz;
use App\Models\Category;
use App\Models\User;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guru = User::where('role', 'guru')->first();
        $cats = Category::all();

        $quizzes = [
            [
                'title' => 'Aljabar Dasar',
                'description' => 'Quiz mengenai persamaan linier dan variabel.',
                'category_id' => $cats->where('name', 'Matematika')->first()->id,
                'duration_minutes' => 30,
            ],
            [
                'title' => 'English Grammar 101',
                'description' => 'Quiz tenses dasar (Present, Past, Future).',
                'category_id' => $cats->where('name', 'Bahasa Inggris')->first()->id,
                'duration_minutes' => 45,
            ],
            [
                'title' => 'Tata Surya',
                'description' => 'Mengenal planet-planet di sistem tata surya kita.',
                'category_id' => $cats->where('name', 'Sains (IPA)')->first()->id,
                'duration_minutes' => 20,
            ],
        ];

        foreach ($quizzes as $quiz) {
            Quiz::create(array_merge($quiz, [
                'created_by' => $guru->id,
                'is_active' => true,
            ]));
        }
    }
}
