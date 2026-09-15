<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'is_active',
        'share_history',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'share_history' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_room_user')->withPivot('is_blocked')->withTimestamps();
    }
}
