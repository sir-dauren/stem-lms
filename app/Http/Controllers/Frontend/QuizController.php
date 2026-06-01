<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    /** Catalog of standalone "check your knowledge" tests. */
    public function index()
    {
        $quizzes = Quiz::where('is_published', true)->where('is_standalone', true)
            ->with('category')->withCount('questions')->latest()->paginate(12);

        return view('frontend.quiz.index', compact('quizzes'));
    }

    public function show(Quiz $quiz)
    {
        abort_unless($quiz->is_published, 404);
        $quiz->load('questions.options');

        $lastAttempt = $quiz->attempts()
            ->where('user_id', Auth::id())->latest('completed_at')->first();

        return view('frontend.quiz.show', compact('quiz', 'lastAttempt'));
    }

    public function submit(Request $request, Quiz $quiz)
    {
        abort_unless($quiz->is_published, 404);
        $quiz->load('questions.options');

        $answers = $request->input('answers', []); // [questionId => optionId | [optionIds]]
        $earned = 0;
        $totalPoints = 0;
        $review = [];

        foreach ($quiz->questions as $question) {
            $totalPoints += $question->points;
            $correct = $question->correctOptionIds();
            sort($correct);

            $given = $answers[$question->id] ?? [];
            $given = array_values(array_filter(array_map('intval', (array) $given)));
            sort($given);

            $isCorrect = $given === $correct && ! empty($given);
            if ($isCorrect) $earned += $question->points;

            $review[$question->id] = [
                'given'      => $given,
                'correct'    => $correct,
                'is_correct' => $isCorrect,
            ];
        }

        $score = $totalPoints ? (int) round($earned / $totalPoints * 100) : 0;
        $passed = $score >= $quiz->passing_score;

        $attempt = QuizAttempt::create([
            'user_id'      => Auth::id(),
            'quiz_id'      => $quiz->id,
            'score'        => $score,
            'passed'       => $passed,
            'answers'      => $review,
            'completed_at' => now(),
        ]);

        return redirect()->route('quizzes.result', $attempt);
    }

    public function result(QuizAttempt $attempt)
    {
        abort_unless($attempt->user_id === Auth::id(), 403);
        $attempt->load('quiz.questions.options');

        return view('frontend.quiz.result', compact('attempt'));
    }
}
