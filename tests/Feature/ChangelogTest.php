<?php

use App\Models\ChangelogJob;
use App\Services\Llm\FakeLlmClient;
use App\Services\Llm\LlmClient;
use Database\Seeders\HarborDeskChangelogSeeder;

it('binds the local fake llm client when no Gemini key is configured', function () {
    config(['services.gemini.api_key' => null]);

    expect(app(LlmClient::class))->toBeInstanceOf(FakeLlmClient::class);
});

it('seeds two fictional Harbor Desk examples including a breaking change', function () {
    $this->seed(HarborDeskChangelogSeeder::class);

    expect(ChangelogJob::query()->where('is_demo', true)->count())->toBe(2)
        ->and(ChangelogJob::query()->where('title', 'Harbor Desk 2.4.0')->firstOrFail()->summary->breaking)
        ->toBe(['Replace the legacy `ticket_owner` field with `assignee_id` in the Tickets API.']);
});

it('lets guests submit a changelog and renders grouped notes with a 200 response', function () {
    $response = $this->post('/', [
        'raw_text' => "## [1.0.0]\n\n### Breaking changes\n- Remove the old API.\n\n### Added\n- Add team spaces.\n\n### Fixed\n- Fix sign in.",
        'rewrite' => false,
    ]);

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Welcome')
            ->where('result.breaking', ['Remove the old API.'])
            ->where('result.features', ['Add team spaces.'])
            ->where('result.fixes', ['Fix sign in.']));

    expect(ChangelogJob::query()->where('status', 'completed')->count())->toBe(1);
});

it('sends parsed groups to the llm rewrite client', function () {
    $fake = new FakeLlmClient;
    app()->instance(LlmClient::class, $fake);

    $response = $this->post('/', [
        'raw_text' => "## [1.0.0]\n\n### Added\n- Add team spaces.",
        'rewrite' => true,
    ]);

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('result.rewritten_markdown', "## Features\n- Add team spaces."));

    expect($fake->lastGroups)->toBe([
        'breaking' => [],
        'features' => ['Add team spaces.'],
        'fixes' => [],
    ]);
});

it('keeps parsed results available when an llm rewrite fails', function () {
    $failingClient = new class implements LlmClient
    {
        public function rewrite(array $groups): string
        {
            throw new RuntimeException('Provider timeout');
        }
    };
    app()->instance(LlmClient::class, $failingClient);

    $this->post('/', [
        'raw_text' => "## [1.0.0]\n\n### Fixed\n- Fix sign in.",
        'rewrite' => true,
    ])->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('result.fixes', ['Fix sign in.'])
            ->where('result.rewritten_markdown', null)
            ->where('rewriteError', 'The rewrite service is unavailable right now. Your parsed release notes are still ready.'));
});
