<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChangelogSummary extends Model
{
    protected $fillable = [
        'changelog_job_id',
        'breaking',
        'features',
        'fixes',
        'rewritten_markdown',
    ];

    protected function casts(): array
    {
        return [
            'breaking' => 'array',
            'features' => 'array',
            'fixes' => 'array',
        ];
    }

    /** @return BelongsTo<ChangelogJob, $this> */
    public function job(): BelongsTo
    {
        return $this->belongsTo(ChangelogJob::class, 'changelog_job_id');
    }
}
