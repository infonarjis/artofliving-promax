<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiMatrimonyDataService
{
    private string $apiKey;
    private string $apiUrl;
    private string $model;

    public function __construct()
    {
        $configArr = _getSiteSetting();
        $this->apiKey = $configArr['gemini_api_key'];
        $this->model  = 'gemini-3.6-flash';
        $this->apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
    }

    /**
     * Generate full matrimonial page content for a given topic.
     *
     * @param  string $topic  e.g. "India Matrimony", "Tamil Brahmin Matrimony", "Muslim Matrimony"
     * @return array
     * @throws Exception
     */
    public function generatePage(string $topic): array
    {
        $prompt   = $this->buildPrompt($topic);
        $response = Http::timeout(30)
            ->post("{$this->apiUrl}?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    // 'temperature'      => 0.7,
                    'maxOutputTokens'  => 3072,
                    // 'responseMimeType' => 'application/json', // force JSON-only output
                    'thinkingConfig'   => [
                        // 'thinkingBudget' => 0, // disable thinking tokens eating into maxOutputTokens
                        'thinkingLevel' => 'minimal',
                    ],
                ],
            ]);

        if ($response->failed()) {
            Log::error('Gemini Matrimony Page API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new Exception('Gemini API request failed with status: ' . $response->status());
        }

        $result       = $response->json();
        $finishReason = $result['candidates'][0]['finishReason'] ?? null;
        $text         = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if (empty($text)) {
            throw new Exception('Gemini returned an empty response.');
        }

        if ($finishReason === 'MAX_TOKENS') {
            Log::error('Gemini Matrimony Page truncated', ['raw' => $text]);
            throw new Exception('Gemini response was truncated (hit maxOutputTokens). Increase the token limit.');
        }

        return $this->parseResponse($text);
    }

    /**
     * Build the prompt sent to Gemini.
     */
    private function buildPrompt(string $topic): string
    {
        $configArr = _getSiteSetting();
        $brand = $configArr['web_name'];

        return "You are an expert SEO specialist for \"{$brand}\", an Indian matrimonial website.\n\n"
            . "Generate complete SEO metadata for the following page topic: \"{$topic}\"\n\n"
            . "CRITICAL RULES:\n"
            . "- Use brand name \"{$brand}\" exactly as-is everywhere.\n"
            . "- NEVER use placeholders like [Brand], [Website], or similar.\n"
            . "- Output MUST be valid JSON only (no markdown, no backticks, no explanation).\n"
            . "- All JSON strings must be properly escaped and valid.\n"
            . "- Do not include trailing commas.\n\n"
            . "CONTENT GUIDELINES:\n"
            . "- Write in clear, natural English suitable for Indian audience.\n"
            . "- Avoid keyword stuffing; keep SEO natural and readable.\n\n"
            . "FIELD RULES:\n"
            . "- pagename: short, clean display name (max 5 words)\n"
            . "- title: H1 title, 60–80 characters, include matrimony intent\n"
            . "- matrimony_description: 250–350 words, 3–4 paragraphs, no bullets\n"
            . "- meta_title: 50–60 characters, SEO optimized\n"
            . "- meta_keyword: 10–15 relevant keywords, comma separated, no repetition\n"
            . "- meta_description: 150–160 characters, compelling CTA, include trust/verification angle\n\n"
            . "Return ONLY this JSON structure:\n"
            . "{\n"
            . "  \"pagename\": \"\",\n"
            . "  \"title\": \"\",\n"
            . "  \"matrimony_description\": \"\",\n"
            . "  \"meta_title\": \"\",\n"
            . "  \"meta_keyword\": \"\",\n"
            . "  \"meta_description\": \"\"\n"
            . "}";
    }

    /**
     * Parse and validate the JSON response from Gemini.
     */
    private function parseResponse(string $text): array
    {
        // Strip any accidental markdown fences Gemini may add
        $clean = preg_replace('/```json|```/i', '', $text);
        $clean = trim($clean);

        $data = json_decode($clean, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Gemini Matrimony Page parse error', ['raw' => $text]);
            throw new Exception('Failed to parse Gemini response as JSON.');
        }

        $required = ['pagename', 'title', 'matrimony_description', 'meta_title', 'meta_keyword', 'meta_description'];

        foreach ($required as $key) {
            if (empty($data[$key])) {
                throw new Exception("Gemini response missing required field: {$key}");
            }
        }

        return $data;
    }
}
