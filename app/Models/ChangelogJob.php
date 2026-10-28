<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChangelogJob extends Model
{
    protected $fillable = [
        'title',
        'raw_text',
        'status',
        'is_demo',
    ];

    protected function casts(): array
    {
        return [
            'is_demo' => 'boolean',
        ];
    }

    /** @return HasOne<ChangelogSummary, $this> */
    public function summary(): HasOne
    {
        return $this->hasOne(ChangelogSummary::class);
    }
}
