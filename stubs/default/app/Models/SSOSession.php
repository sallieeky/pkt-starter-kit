<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SSOSession extends Model
{
    use HasFactory;

    protected $table = 'sso_sessions';

    protected $fillable = [
        'SESSION_ID', 'USER_ID', 'TOKEN', 'USER_ALIASES'
    ];
}
