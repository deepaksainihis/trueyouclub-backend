<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seminar extends Model
{
    use HasFactory, SoftDeletes;

    public $table = 'seminars';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'title',
        'total_ticket',
        'ticket_price',
        'start_date',
        'start_time',
        'end_time',
        'venue',
        'status',
        'cron_status',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (Seminar $model) {
            $model->created_by = auth()->user()->id;
        });

        static::deleting(function ($model) {
            if ($model->seminarImage) {
                $uploadImageId = $model->seminarImage->id;
                deleteFile($uploadImageId);
            }
        });
    }

    public function uploads()
    {
        return $this->morphMany(Uploads::class, 'uploadsable');
    }

    public function seminarImage()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('type', 'seminar');
    }

    public function getImageUrlAttribute()
    {
        if ($this->seminarImage) {
            return $this->seminarImage->file_url;
        }
        return "";
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bookings()
    {
        return $this->morphMany(Booking::class, 'bookingable')->where('type', 'seminar');
    }

}
