<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{

    public $table = 'bookings';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'user_details' => 'json',
        'bookingable_details'=>'json',
    ];

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'user_details',
        'bookingable',
        'bookingable_details',
        'booking_number',
        'type',
        'passcode',
        'status',
        'created_at',
        'updated_at',
    ];

    public function bookingable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seminar()
    {
        return $this->belongsTo(Seminar::class, 'bookingable_id');
    }
}
