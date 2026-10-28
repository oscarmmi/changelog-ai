<?php

namespace App\Services\Llm;

class FakeLlmClient implements LlmClient
{
    /** @var array{breaking: list<string>, features: list<string>, fixes: list<string>}|null */
    public ?array $lastGroups = null;

    /** @param array{breaking: list<string>, features: list<string>, fixes: list<string>} $groups */
    public function rewrite(array $groups): string
    {
        $this->lastGroups = $groups;
        $sections = [];

        foreach ([
            'breaking' => 'Breaking changes',
            'features' => 'Features',
            'fixes' => 'Fixes',
        ] as $key => $title) {
            if ($groups[$key] === []) {
                continue;
            }

            $sections[] = '## '.$title."\n".implode("\n", array_map(
                static fn (string $item): string => '- '.$item,
                $groups[$key],
            ));
        }

        return implode("\n\n", $sections);
    }
}
