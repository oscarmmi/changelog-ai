<?php

namespace Database\Seeders;

use App\Models\ChangelogJob;
use App\Services\Changelog\ChangelogParser;
use Illuminate\Database\Seeder;

class HarborDeskChangelogSeeder extends Seeder
{
    public function run(ChangelogParser $parser): void
    {
        $examples = [
            [
                'title' => 'Harbor Desk 2.4.0',
                'raw_text' => <<<'CHANGELOG'
# Harbor Desk

## [2.4.0] - 2026-10-14

### Breaking Changes
- Replace the legacy `ticket_owner` field with `assignee_id` in the Tickets API.

### Added
- Add saved views so support teams can return to their preferred ticket filters.
- Add keyboard shortcuts for assigning and closing a conversation.

### Fixed
- Prevent duplicate email notifications when a ticket is reassigned twice.
CHANGELOG,
            ],
            [
                'title' => 'Harbor Desk 2.3.2',
                'raw_text' => <<<'CHANGELOG'
# Harbor Desk

## [2.3.2] - 2026-09-28

### Added
- Add a compact density option to the inbox list.

### Changed
- Keep the selected team filter when navigating between tickets.

### Fixed
- Correct the unread count after a conversation is archived.
- Restore CSV export for filtered ticket lists.
CHANGELOG,
            ],
        ];

        foreach ($examples as $example) {
            $job = ChangelogJob::query()->updateOrCreate(
                ['title' => $example['title'], 'is_demo' => true],
                [
                    'raw_text' => $example['raw_text'],
                    'status' => 'completed',
                ],
            );
            $job->summary()->updateOrCreate([], [
                ...$parser->parse($example['raw_text']),
                'rewritten_markdown' => null,
            ]);
        }
    }
}
