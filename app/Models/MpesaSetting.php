<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MpesaSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'consumer_key',
        'consumer_secret',
        'shortcode',
        'passkey',
        'base_url',
        'callback_url',
        'environment',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'consumer_secret',
        'passkey',
    ];

    /**
     * Get the active M-Pesa configuration
     */
    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }
}