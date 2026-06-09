<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

#[Fillable('title', 'description', 'starts_at', 'ends_at', 'speaker', 'speaker_background')]
#[WithoutTimestamps]
class Talk extends Model
{
    use HasFactory;

    public function conference(): BelongsTo {
        return $this->belongsTo(Conference::class);
    }

    protected function casts(): array {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }
}
