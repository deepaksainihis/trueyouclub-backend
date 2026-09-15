<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class VolunteerAvailability extends Model
{
    use SoftDeletes;

    public $table = 'volunteer_availabilities';
    
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'date',
    ];

    protected $fillable = [
        'volunteer_id',
        'date',
        'start_time',
        'end_time',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (VolunteerAvailability $model) {
            $model->volunteer_id = auth()->user()->id;
        });

    }

    public function user()
    {
        return $this->belongsTo(User::class, 'volunteer_id');
    }


    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }
}
