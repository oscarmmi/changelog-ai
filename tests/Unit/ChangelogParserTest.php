<?php

use App\Services\Changelog\ChangelogParser;

it('extracts breaking changes, features, and fixes from Keep a Changelog headings', function () {
    $result = (new ChangelogParser)->parse(<<<'CHANGELOG'
## [Unreleased]

### Breaking Changes
- Remove the v1 API endpoint.

### Added
- Add project labels.

### Changed
- Improve search ranking.

### Fixed
- Resolve a timezone display bug.
CHANGELOG);

    expect($result)->toBe([
        'breaking' => ['Remove the v1 API endpoint.'],
        'features' => ['Add project labels.', 'Improve search ranking.'],
        'fixes' => ['Resolve a timezone display bug.'],
    ]);
});

it('ignores list items outside supported change sections', function () {
    $result = (new ChangelogParser)->parse(<<<'CHANGELOG'
## [Unreleased]
- This is introductory text.

### Fixed
- Correct the dashboard total.

## [1.0.0]
- This unclassified item is not a fix from the previous release.
CHANGELOG);

    expect($result['fixes'])->toBe(['Correct the dashboard total.']);
});
