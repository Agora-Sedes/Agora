<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'description', 'starts_at', 'ends_at'])]
#[WithoutTimestamps]
class Conference extends Model
{
    use HasFactory;

    public function attendants(): HasMany {
        return $this->hasMany(Attendant::class)->chaperone();
    }

    public function talks(): HasMany {
        return $this->hasMany(Talk::class)->chaperone();
    }

    protected function casts(): array {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }
}
