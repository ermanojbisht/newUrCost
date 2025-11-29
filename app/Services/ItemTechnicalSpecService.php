<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemTechnicalSpec;
use LucianoTonet\GroqPHP\Groq;
use Illuminate\Support\Facades\Log;

class ItemTechnicalSpecService
{
    protected $groq;

    public function __construct()
    {
        $apiKey = env('GROQ_API_KEY');
        if (!$apiKey) {
            throw new \Exception('GROQ_API_KEY is not set in .env');
        }
        $this->groq = new Groq($apiKey);
    }

    public function generateSpecs(Item $item)
    {
        $name = $item->name;
        $prompt = "Create a detailed technical specification document for the **$name** in simple language.
        
        Return the output strictly as a valid JSON object with the following keys:
        - introduction (string): A brief overview.
        - specifications (array of strings): Detailed components, materials, dimensions.
        - tests_frequency (array of objects): Each object having 'test' (string) and 'frequency' (string).
        - dos_donts (object): With 'dos' (array of strings) and 'donts' (array of strings).
        - execution_sequence (array of strings): Step-by-step process.
        - precautionary_measures (array of strings): Safety measures.
        - reference_links (array of objects): Each object having 'title' (string) and 'url' (string).

        Ensure the content is accurate, relevant to civil engineering, and suitable for field engineers.
        Do not include any markdown formatting in the JSON values, just plain text.";

        try {
            $response = $this->groq->chat()->completions()->create([
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful civil engineering assistant. Output strictly valid JSON.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'response_format' => ['type' => 'json_object']
            ]);

            $content = $response['choices'][0]['message']['content'];
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from AI: ' . json_last_error_msg());
            }

            return ItemTechnicalSpec::updateOrCreate(
                ['item_id' => $item->id],
                $data
            );

        } catch (\Exception $e) {
            Log::error('Error generating item specs: ' . $e->getMessage());
            throw $e;
        }
    }
}
