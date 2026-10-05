<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiSeoService
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
     * Generate full SEO data for a given page topic.
     *
     * @param  string $topic
     * @return array
     * @throws Exception
     */
    public function generateSeo(string $topic): array
    {
        $prompt = $this->buildPrompt($topic);

        $response = Http::timeout(30)
            ->retry(3, 1000)
            ->post("{$this->apiUrl}?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'maxOutputTokens' => 3072,
                ],
            ]);

        if ($response->failed()) {
            Log::error('Gemini SEO API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new Exception('Gemini API request failed: ' . $response->status());
        }

        $result = $response->json();

        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if (empty($text)) {
            throw new Exception('Gemini returned an empty response.');
        }

        return $this->parseResponse($text);
    }

    /**
     * Build the prompt sent to Gemini.
     */
    private function buildPrompt(string $topic): string
    {
        $configArr = _getSiteSetting();
        $siteName = $configArr['web_name'];
        $siteUrl = url('/');

        return <<<PROMPT
            You are an expert SEO specialist for "{$siteName}", an Indian matrimonial website.

            Website URL: {$siteUrl}

            Generate highly optimized SEO metadata for the following page topic:

            "{$topic}"

            STRICT RULES:
            - Output ONLY valid JSON (no markdown, no explanation, no backticks)
            - JSON must be 100% parseable by PHP json_decode()
            - Do NOT add any extra keys other than specified below
            - Do NOT include trailing commas
            - Keep content SEO-optimized for Indian matrimonial search intent
            - Use natural language, not keyword stuffing

            OUTPUT FORMAT (EXACT KEYS ONLY):

            {
            "seo_title": "50–60 characters, keyword-rich, attractive",
            "seo_description": "150–160 characters, compelling and CTR-focused",
            "seo_keywords": "8–12 relevant comma-separated keywords for matrimonial niche",
            "og_title": "Same intent as seo_title but optimized for social sharing",
            "og_description": "1–2 engaging sentences for social previews",
            "meta_robots": "index,follow",
            "schema_json": "STRINGIFIED valid JSON-LD WebPage schema ONLY (must be escaped as a single string)"
            }

            SCHEMA RULES:
            - schema_json must be a SINGLE escaped JSON string
            - Must follow Schema.org WebPage structure
            - Must include name, url, and description
            - Must reference "{$siteName}" and "{$siteUrl}"
            - Must NOT break JSON formatting

            QUALITY RULES:
            - Focus on Indian matrimonial SEO intent
            - Prefer keywords like: marriage, bride, groom, matrimony, profiles, matchmaking
            - Avoid generic or unrelated SEO terms
            PROMPT;
    }

    /**
     * Parse and validate the JSON response from Gemini.
     */
    private function parseResponse(string $text): array
    {
        // 1. Remove markdown fences
        $clean = preg_replace('/```(?:json)?|```/i', '', $text);
        $clean = trim($clean);

        // 2. Find first JSON start
        $start = strpos($clean, '{');

        if ($start === false) {
            Log::error('No JSON start found', ['raw' => $text]);
            throw new Exception('No JSON found in response.');
        }

        $json = substr($clean, $start);

        // 3. Try to safely extract balanced JSON
        $depth = 0;
        $end = null;

        for ($i = 0; $i < strlen($json); $i++) {
            if ($json[$i] === '{') {
                $depth++;
            } elseif ($json[$i] === '}') {
                $depth--;
                if ($depth === 0) {
                    $end = $i;
                    break;
                }
            }
        }

        if ($end === null) {
            Log::error('Unbalanced JSON from Gemini', ['raw' => $text]);
            throw new Exception('Invalid JSON structure from Gemini.');
        }

        $json = substr($json, 0, $end + 1);

        // 4. Decode
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON decode error', [
                'error' => json_last_error_msg(),
                'raw' => $json,
            ]);

            throw new Exception('Failed to parse Gemini response as JSON.');
        }

        return $data;
    }
}
