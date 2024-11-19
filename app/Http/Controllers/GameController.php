<?php
namespace App\Http\Controllers;
use App\Services\GeminiService;
use Illuminate\Http\Request;
// use Intervention\Image\Facades\Image;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http; // {{ edit_1 }}
use App\Http\Controllers\Controllers\Console;
use Illuminate\Support\Facades\Storage; // {{ edit_1 }}
use Intervention\Image\ImageManagerStatic as Image; // {{ edit_1 }}

class GameController extends Controller
{
    public function index()
    {
        $num1 = rand(1, 20);
        $num2 = rand(1, 10);
        
        session(['correct_answer' => $num1 * $num2]);
        
        return view('game', compact('num1', 'num2'));
    }

    public function checkAnswer(Request $request)
    {
        $correct_answer = session('correct_answer');
        $user_answer = $request->answer;
        
        $isCorrect = (int)$user_answer === $correct_answer;
        
         // Regenerate numbers for the next question
        $num1 = rand(1, 20);
        $num2 = rand(1, 10);
        session(['correct_answer' => $num1 * $num2]);

        return response()->json([
            'correct' => $isCorrect,
            'correct_answer' => $correct_answer,
            'num1' => $num1, // New number 1
            'num2' => $num2  // New number 2
        ]);
    }
  
    // public function upload(Request $request)
    // {
    //     // Validate the incoming request
    //     $request->validate([
    //         'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ]);

    //     // Get the uploaded file
    //     $image = $request->file('image');

    //     // Send the image to the Python backend
    //     $response = Http::attach('image', file_get_contents($image), $image->getClientOriginalName())
    //         ->post('http://127.0.0.1:5000/describe'); // Replace with your actual backend URL

    //     // Handle the response from the Python backend
    //     if ($response->successful()) {
    //         $data = $response->json();
    //         return response()->json($data); // Return the OCR text as JSON
    //     } else {
    //         return response()->json(['error' => 'Failed to process image'], 500);
    //     }
    // }

}