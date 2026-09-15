<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportedMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'firebase_message_id',
        'room_id',
        'reported_by',
        'reason'
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function room()
    {
        return $this->belongsTo(ChatRoom::class, 'room_id');
    }
}
