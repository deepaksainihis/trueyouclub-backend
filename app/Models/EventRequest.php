<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class EventRequest extends Model
{
    use HasFactory,SoftDeletes;

    public $table = 'event_requests';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [        
        'event_id',
        'volunteer_id',
        'custom_message',
        'attempts',
        'status',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (EventRequest $model) {
            $model->created_by = auth()->user()->id;
        });
        
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function volunteer()
    {
        return $this->belongsTo(User::class, 'volunteer_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
