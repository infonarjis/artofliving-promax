<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiGenerateService
{
    /**
     * Word limits per prompt type — used both to build instructions
     * and to hard-enforce the limit after the API responds.
     */
    private const WORD_LIMITS = [
        'about_me' => 100,
        'partner_preference' => 100,
        'default' => 200,
    ];

    public function generate(string $type, $member = []): ?string
    {
        $configArr = _getSiteSetting();

        $geminiApiKey = $configArr['gemini_api_key'] ?? null;

        if (blank($geminiApiKey)) {
            Log::error('Gemini API key is missing');

            return null;
        }

        $geminiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent';

        $prompt = $this->buildPrompt($type, $member);

        $response = Http::timeout(60)
            ->post($geminiUrl . '?key=' . $geminiApiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt,
                            ],
                        ],
                    ],
                ],
                'generationConfig' => [
                    // Hard ceiling on tokens. ~1.3-1.5 tokens per word, so this is
                    // generous enough to avoid mid-sentence cutoffs; word-level
                    // enforcement happens in PHP afterwards.
                    'maxOutputTokens' => 350,
                    'thinkingConfig' => [
                        // This is a formatted text-generation task, not an agentic /
                        // multi-step task, so we don't want reasoning tokens eating
                        // into the output budget.
                        'thinkingLevel' => 'minimal',
                    ],
                ],
            ]);

        if ($response->failed()) {
            Log::error('Gemini API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        $candidate = $response->json('candidates.0');

        $text = $candidate['content']['parts'][0]['text'] ?? null;

        $finishReason = $candidate['finishReason'] ?? null;

        if (blank($text)) {
            Log::warning('Gemini API returned no text', [
                'type' => $type,
                'finishReason' => $finishReason,
                'body' => $response->body(),
            ]);

            return null;
        }

        if ($finishReason === 'MAX_TOKENS') {
            Log::warning('Gemini response was cut off at MAX_TOKENS', [
                'type' => $type,
            ]);
        }

        return $this->enforceWordLimit(trim($text), $type);
    }

    /**
     * Hard safety net — trims to the configured word limit regardless
     * of whether the model followed the prompt instructions.
     */
    private function enforceWordLimit(string $text, string $type): string
    {
        $max = self::WORD_LIMITS[$type] ?? self::WORD_LIMITS['default'];

        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        if (count($words) <= $max) {
            return $text;
        }

        $trimmed = implode(' ', array_slice($words, 0, $max));

        // Avoid ending mid-sentence on a comma/dangling word.
        $trimmed = rtrim($trimmed, ',;:- ');

        if (!in_array(substr($trimmed, -1), ['.', '!', '?'], true)) {
            $trimmed .= '.';
        }

        return $trimmed;
    }

    /**
     * All AI prompts live here
     */
    private function buildPrompt(string $type, $member): string
    {
        $education = !empty($member->education_level_names)
            ? implode(', ', $member->education_level_names)
            : 'Not specified';

        switch ($type) {

            case 'about_me':
                return <<<PROMPT
        You are a professional matrimonial profile writer.

        Write an "About Me" section using the details below.

        Details:
        - Full Name: {$member->fullname}
        - Birth Date: {$member->birthdate}
        - Education: {$education}
        - Occupation: {$member->occupationData?->translated_name}
        - Employer: {$member->employeeInData?->translated_name}
        - Designation: {$member->designationLevelData?->translated_name}

        Instructions:
        - Write ONLY one paragraph.
        - The response MUST be between 120 and 150 words.
        - NEVER exceed 150 words.
        - Count the words before returning the response.
        - If the response is over 150 words, rewrite it until it is within the limit.
        - Use a polite, family-oriented, positive, and natural tone.
        - Avoid repetition.
        - Do not use headings, bullet points, numbering, or quotation marks.
        - Output ONLY the final description text.
        PROMPT;

            case 'partner_preference':
                return <<<PROMPT
        You are a professional matrimonial profile writer.

        Write a "Partner Preference" section.

        Instructions:
        - Write ONLY one paragraph.
        - The response MUST be between 100 and 130 words.
        - NEVER exceed 130 words.
        - Count the words before returning the response.
        - If the response is over 130 words, rewrite it until it is within the limit.
        - Use a respectful, realistic, family-oriented, and warm tone.
        - Avoid generic or repetitive sentences.
        - Do not use headings, bullet points, numbering, or quotation marks.
        - Output ONLY the final text.
        PROMPT;

            default:
                return <<<PROMPT
        {$member->prompt}

        Instructions:
        - Keep the response between 150 and 200 words.
        - NEVER exceed 200 words.
        - Count the words before returning the response.
        - If the response exceeds 200 words, rewrite it until it is within the limit.
        - Output ONLY the final response.
        PROMPT;
        }
    }
}