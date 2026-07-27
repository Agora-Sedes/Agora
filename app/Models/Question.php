<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['conference_id', 'body', 'status', 'pinned', 'position'])]
class Question extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'pinned' => 'boolean',
        ];
    }

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('pinned', 'desc')->orderBy('position')->orderBy('created_at', 'desc');
    }
}
