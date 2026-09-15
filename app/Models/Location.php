<?php

namespace App\Models;

use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory, Sluggable, SoftDeletes;
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'status',
        'created_by'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (Location $model) {
            $model->created_by = auth()->user()->id;
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
