<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['conference_id', 'body', 'status', 'is_pinned', 'sort_order'])]
class Question extends Model
{
    use HasFactory;

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
