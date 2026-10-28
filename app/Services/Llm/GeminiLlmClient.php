<?php

namespace App\Services\Llm;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiLlmClient implements LlmClient
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $model,
    ) {}

    /** @param array{breaking: list<string>, features: list<string>, fixes: list<string>} $groups */
    public function rewrite(array $groups): string
    {
        $response = Http::connectTimeout(4)
            ->timeout(12)
            ->withHeader('x-goog-api-key', $this->apiKey)
            ->post('https://generativelanguage.googleapis.com/v1beta/models/'.rawurlencode($this->model).':generateContent', [
                'contents' => [[
                    'parts' => [[
                        'text' => "Rewrite the release notes as concise, polished Markdown. Preserve every factual detail, do not invent claims, and organize items under Breaking changes, Features, and Fixes. Return only Markdown.\n\nParsed changelog groups (JSON):\n".json_encode($groups, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                    ]],
                ]],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'maxOutputTokens' => 1200,
                ],
            ]);

        $response->throw();

        $text = $response->json('candidates.0.content.parts.0.text');

        if (! is_string($text) || trim($text) === '') {
            throw new RuntimeException('Gemini returned an empty rewrite.');
        }

        return trim($text);
    }
}
