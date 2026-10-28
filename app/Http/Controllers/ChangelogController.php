<?php

namespace App\Http\Controllers;

use App\Models\ChangelogJob;
use App\Services\Changelog\ChangelogParser;
use App\Services\Llm\LlmClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ChangelogController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Welcome', [
            'examples' => ChangelogJob::query()
                ->where('is_demo', true)
                ->orderByDesc('id')
                ->get(['id', 'title', 'raw_text'])
                ->map(fn (ChangelogJob $job): array => [
                    'id' => $job->id,
                    'title' => $job->title,
                    'raw_text' => $job->raw_text,
                ])
                ->values(),
            'result' => null,
            'rewriteError' => null,
            'llmMode' => $this->llmMode(),
        ]);
    }

    public function store(Request $request, ChangelogParser $parser, LlmClient $llm): Response
    {
        $validated = $request->validate([
            'raw_text' => ['required', 'string', 'max:50000'],
            'rewrite' => ['sometimes', 'boolean'],
        ]);

        $groups = $parser->parse($validated['raw_text']);
        $job = ChangelogJob::query()->create([
            'raw_text' => $validated['raw_text'],
            'status' => 'completed',
        ]);
        $rewrittenMarkdown = null;
        $rewriteError = null;

        if ($request->boolean('rewrite')) {
            try {
                $rewrittenMarkdown = $llm->rewrite($groups);
            } catch (Throwable $exception) {
                Log::warning('Changelog rewrite failed; showing deterministic parse instead.', [
                    'changelog_job_id' => $job->id,
                    'exception' => $exception->getMessage(),
                ]);
                $rewriteError = 'The rewrite service is unavailable right now. Your parsed release notes are still ready.';
            }
        }

        $summary = $job->summary()->create([
            ...$groups,
            'rewritten_markdown' => $rewrittenMarkdown,
        ]);

        return Inertia::render('Welcome', [
            'examples' => ChangelogJob::query()
                ->where('is_demo', true)
                ->orderByDesc('id')
                ->get(['id', 'title', 'raw_text'])
                ->map(fn (ChangelogJob $example): array => [
                    'id' => $example->id,
                    'title' => $example->title,
                    'raw_text' => $example->raw_text,
                ])
                ->values(),
            'result' => [
                'job_id' => $job->id,
                'breaking' => $summary->breaking,
                'features' => $summary->features,
                'fixes' => $summary->fixes,
                'rewritten_markdown' => $summary->rewritten_markdown,
            ],
            'rewriteError' => $rewriteError,
            'llmMode' => $this->llmMode(),
        ]);
    }

    private function llmMode(): string
    {
        return filled(config('services.gemini.api_key')) ? 'Gemini' : 'Local preview';
    }
}
