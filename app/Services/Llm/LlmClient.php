<?php

namespace App\Services\Llm;

interface LlmClient
{
    /**
     * @param  array{breaking: list<string>, features: list<string>, fixes: list<string>}  $groups
     */
    public function rewrite(array $groups): string;
}
