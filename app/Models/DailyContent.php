<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'scheduled_date',
        'type',
        'title',
        'description',
        'media_path'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
    ];
}
