<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserToken extends Model
{
    public $table = 'user_tokens';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'email',
        'seminar_id',
        'token',
        'type',
        'created_at',
        'updated_at',
    ];
}
