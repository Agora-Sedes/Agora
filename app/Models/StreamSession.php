<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['attendant_id', 'conference_id', 'session_token', 'last_seen_at'])]
class StreamSession extends Model
{
    protected function casts(): array {
        return [
            'last_seen_at' => 'datetime',
        ];
    }

    public function attendant(): BelongsTo {
        return $this->belongsTo(Attendant::class);
    }

    public function conference(): BelongsTo {
        return $this->belongsTo(Conference::class);
    }
}
