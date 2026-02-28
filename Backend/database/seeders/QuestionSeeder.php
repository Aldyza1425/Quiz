<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuestionOption;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Aljabar Dasar
        $quiz1 = Quiz::where('title', 'Aljabar Dasar')->first();
        if ($quiz1) {
            $q1 = Question::create([
                'quiz_id' => $quiz1->id,
                'question_text' => 'Berapakah nilai x dari persamaan 2x + 4 = 10?',
                'type' => 'multiple_choice',
                'points' => 10,
                'order' => 1,
            ]);

            QuestionOption::create(['question_id' => $q1->id, 'option_text' => '2', 'is_correct' => false, 'order' => 1]);
            QuestionOption::create(['question_id' => $q1->id, 'option_text' => '3', 'is_correct' => true, 'order' => 2]);
            QuestionOption::create(['question_id' => $q1->id, 'option_text' => '4', 'is_correct' => false, 'order' => 3]);
            QuestionOption::create(['question_id' => $q1->id, 'option_text' => '5', 'is_correct' => false, 'order' => 4]);

            $q2 = Question::create([
                'quiz_id' => $quiz1->id,
                'question_text' => 'Apakah x = 5 adalah solusi dari x - 2 = 3?',
                'type' => 'true_false',
                'points' => 5,
                'order' => 2,
            ]);
            QuestionOption::create(['question_id' => $q2->id, 'option_text' => 'Benar', 'is_correct' => true, 'order' => 1]);
            QuestionOption::create(['question_id' => $q2->id, 'option_text' => 'Salah', 'is_correct' => false, 'order' => 2]);
        }

        // Tata Surya
        $quiz3 = Quiz::where('title', 'Tata Surya')->first();
        if ($quiz3) {
            $q3 = Question::create([
                'quiz_id' => $quiz3->id,
                'question_text' => 'Planet manakah yang dijuluki si Planet Merah?',
                'type' => 'multiple_choice',
                'points' => 10,
                'order' => 1,
            ]);
            QuestionOption::create(['question_id' => $q3->id, 'option_text' => 'Venus', 'is_correct' => false, 'order' => 1]);
            QuestionOption::create(['question_id' => $q3->id, 'option_text' => 'Mars', 'is_correct' => true, 'order' => 2]);
            QuestionOption::create(['question_id' => $q3->id, 'option_text' => 'Jupiter', 'is_correct' => false, 'order' => 3]);
            QuestionOption::create(['question_id' => $q3->id, 'option_text' => 'Saturnus', 'is_correct' => false, 'order' => 4]);

            $q4 = Question::create([
                'quiz_id' => $quiz3->id,
                'question_text' => 'Sebutkan nama planet terbesar di tata surya kita!',
                'type' => 'short_answer',
                'points' => 15,
                'explanation' => 'Jupiter adalah planet terbesar dengan massa ribuan kali bumi.',
                'order' => 2,
            ]);
            // Short answer doesn't necessarily need QuestionOption, but we can store the "key" there if needed or handle logic in controller.
        }
    }
}
