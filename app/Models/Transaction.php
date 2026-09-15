<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    public $table = 'transactions';
  
    protected $casts = [
        'user_json'=>'json',
        'ticket_json'=>'json',
        'payment_json' => 'json',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'user_json',
        'ticket_id',
        'ticket_json',
        'type',
        'description',
        'payment_intent_id',
        'amount',
        'currency',
        'status',
        'payment_type',
        'payment_method',
        'payment_json',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
