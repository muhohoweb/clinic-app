<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Visit extends Model
{
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }


    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
