<?php

namespace App\Services\Changelog;

class ChangelogParser
{
    /**
     * @return array{breaking: list<string>, features: list<string>, fixes: list<string>}
     */
    public function parse(string $changelog): array
    {
        $groups = [
            'breaking' => [],
            'features' => [],
            'fixes' => [],
        ];
        $activeGroup = null;
        $activeHeadingLevel = null;

        foreach (preg_split('/\R/', $changelog) ?: [] as $line) {
            if (preg_match('/^\s{0,3}(#{1,6})\s+(.+?)\s*#*\s*$/', $line, $heading) === 1) {
                $headingLevel = strlen($heading[1]);
                $headingText = strtolower(trim(strip_tags($heading[2])));
                $recognizedGroup = $this->groupForHeading($headingText);

                if ($recognizedGroup !== null) {
                    $activeGroup = $recognizedGroup;
                    $activeHeadingLevel = $headingLevel;
                } elseif ($activeHeadingLevel !== null && $headingLevel <= $activeHeadingLevel) {
                    $activeGroup = null;
                    $activeHeadingLevel = null;
                }

                continue;
            }

            if ($activeGroup === null || preg_match('/^\s*(?:[-*+]|\d+\.)\s+(.+?)\s*$/', $line, $item) !== 1) {
                continue;
            }

            $text = trim($item[1]);

            if ($text !== '') {
                $groups[$activeGroup][] = $text;
            }
        }

        return $groups;
    }

    /** @return 'breaking'|'features'|'fixes'|null */
    private function groupForHeading(string $heading): ?string
    {
        if (preg_match('/\bbreaking\b/', $heading) === 1) {
            return 'breaking';
        }

        if (preg_match('/\b(?:added|changed)\b/', $heading) === 1) {
            return 'features';
        }

        if (preg_match('/\bfixed\b/', $heading) === 1) {
            return 'fixes';
        }

        return null;
    }
}
