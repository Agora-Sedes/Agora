<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['conference_id', 'is_draft', 'was_present', 'government_id', 'full_name', 'email', 'phone_number'])]
#[WithoutTimestamps]
class Attendant extends Model
{
    public function conference(): BelongsTo {
        return $this->belongsTo(Conference::class);
    }
}
