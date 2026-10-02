<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SitePreference extends Model
{
    use HasFactory;

    protected $table = 'site_preferences';

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'language',
        'theme_mode',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
