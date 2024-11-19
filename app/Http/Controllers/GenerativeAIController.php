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

class GenerativeAIController extends Controller {

public function generateContent(Request $request)
{
    // Log the incoming request
    Log::info('Generating content with prompt:', ['prompt' => $request->input('prompt')]); // {{ edit_1 }}

    // Validate the request
    $request->validate([
        'prompt' => 'required|string', // {{ edit_1 }} - Validate that a prompt is provided
    ]);

    // Prepare the request payload
    $payload = [
        'prompt' => 'Please perform OCR on this image and extract all visible text. List all the text you can find.', // {{ edit_2 }} - Use the fixed prompt directly
        'model' => 'gemini-1.5-flash',
    ];

    // Make request to Google Generative AI API
    $response = Http::withHeaders([
        'Authorization' => env('GEMINI_API_KEY'),
        'Content-Type' => 'application/json',
    ])->post('https://generativeai.googleapis.com/v1/models/gemini-1.5-flash:generateContent', $payload);

      // Log the API response
      Log::info('API response:', ['response' => $response->json()]); // {{ edit_2 }}

    if ($response->successful()) {
        return response()->json([
            'success' => true,
            'data' => $response->json()
        ]);
    }

    return response()->json([
        'success' => false,
        'error' => 'API request failed',
        'details' => $response->json()
    ], 500);
}

// New method to handle chat and image processing
public function chatAndImage(Request $request)
{
    // Validate the request
    $request->validate([
        'message' => 'required|string',
        'image_url' => 'required|url', // URL of the image to process
    ]);

    // Obtain the image
    $imageUrl = $request->input('image_url');
    $imagePath = 'image.jpg';
    file_put_contents($imagePath, file_get_contents($imageUrl)); // Save the image locally

    // Load the image using Intervention Image
    $img = Image::make($imagePath);

    // Generate content using the model
    $model = 'gemini-1.5-flash-latest'; // Specify the model
    $response = Http::withHeaders([
        'Authorization' => env('GEMINI_API_KEY'),
        'Content-Type' => 'application/json',
    ])->post('https://generativeai.googleapis.com/v1/models/' . $model . ':generateContent', [
        'prompt' => 'What is this picture?',
        'image' => base64_encode($img->encode()->__toString()), // Convert image to base64
    ]);

    // Log the API response
    Log::info('Image processing response:', ['response' => $response->json()]); // {{ edit_3 }}

    if ($response->successful()) {
        return response()->json([
            'success' => true,
            'data' => $response->json()
        ]);
    }

    return response()->json([
        'success' => false,
        'error' => 'API request failed',
        'details' => $response->json()
    ], 500);
}
}