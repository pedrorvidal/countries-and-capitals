<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
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
    public function prepareGame(Request $request)
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

        //store quizz in session
        session()->put([
            'quiz' => $quiz,
            'total_questions' => $total_questions,
            'current_question' => 1,
            'correct_answers' => 0,
            'wrong_answers' => 0
        ]);

        return redirect()->route('game');
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
            $question['wrong_answers'] = array_slice($other_capitals, 0, 3);

            // store answer result
            $question['correct'] = null;

            $questions[] = $question;
        }

        return $questions;
    }

    public function game(): View
    {
        $quiz = session('quiz');
        $total_questions = session('total_questions');
        $current_question = session('current_question') - 1;

        // prepare answers to show in view
        $answers = $quiz[$current_question]['wrong_answers'];
        $answers[] = $quiz[$current_question]['correct_answer'];

        shuffle($answers);

        return view('game',)->with([
            'country' => $quiz[$current_question]['country'],
            'totalQuestions' => $total_questions,
            'currentQuestion' => $current_question,
            'answers' => $answers
        ]);
    }

    public function answer($enc_answer)
    {
        try {
            $answer = Crypt::decryptString($enc_answer);
        } catch (\Exception $e) {
            return redirect()->route('game');
        }
        // game logic
        $quiz = session('quiz');
        $current_question = session('current_question') - 1;
        $correct_answer = $quiz[$current_question]['correct_answer'];
        $correct_answers = session('correct_answers');
        $wrong_answers = session('wrong_answers');
        if ($answer == $correct_answer) {
            $correct_answers++;
            $quiz[$current_question]['correct'] = true;
        } else {
            $wrong_answers++;
            $quiz[$current_question]['correct'] = false;
        }
        // update session
        session()->put([
            'quiz' => $quiz,
            'correct_answers' => $correct_answers,
            'wrong_answers' => $wrong_answers
        ]);

        // prepare data to show the correct answer
        $data = [
            'country' => $quiz[$current_question]['country'],
            'correct_answer' => $correct_answer,
            'choice_answer' => $answer,
            'total_questions' => session('total_questions'),
            'current_question' => $current_question
        ];
    }
}
