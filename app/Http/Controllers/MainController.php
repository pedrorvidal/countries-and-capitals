<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class MainController extends Controller
{
    private $_app_data;

    public function __construct()
    {
        // load app_data.php file from app folder
        $this->_app_data = require(app_path('app_data.php'));
    }
    public function startGame(): View
    {
        return view('home');
    }
    public function prepareGame(Request $request): View
    {
        //validate request
        $request->validate(
            [
                'total_questions' => 'required|integer|min:3|max:30',
            ],
            [
                'total_questions.required' => 'O número de questões é obrigatório',
                'total_questions.integer' => 'O número de questões deve ser um número inteiro',
                'total_questions.min' => 'No mínimo :min questões',
                'total_questions.max' => 'No máximo :max questões',
            ]
        );
        // get total questions
        $total_questions = intval($request->input('total_questions'));
        // prepare all the quiz structure
        $quiz = $this->prepareQuiz($total_questions);
        dd($quiz);
    }
    private function prepareQuiz($total_questions)
    {
        $questions = [];
        $total_countries = count($this->_app_data);

        // create countries index for unique questions
        $indexes = range(0, $total_countries - 1);
        shuffle($indexes);
        $indexes = array_slice($indexes, 0, $total_questions);
        $question_number = 1;

        // create array of questions
        foreach ($indexes as $index) {
            $question['question_number'] = $question_number++;
            $question['country'] = $this->_app_data[$index]['country'];
            $question['correct_answer'] = $this->_app_data[$index]['capital'];

            //wrong answers
            $other_capitals = array_column($this->_app_data, 'capital');
            //remove correct answer
            $other_capitals = array_diff($other_capitals, [$question['correct_answer']]);

            shuffle($other_capitals);

        }
        return $questions;
    }
}
