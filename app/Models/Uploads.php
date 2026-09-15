<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Uploads extends Model
{
    protected $appends = ['file_url'];

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'name',
        'uploadsable',
        'file_path',
        'title',
        'original_file_name',
        'type',
        'file_type',
        'extension',
        'orientation',
        'created_at',
        'updated_at',
    ];

    protected static function boot()
    {
        parent::boot();

        self::deleted(function ($model) {
            Storage::disk('public')->delete($model->file_path); 
            // return parent::delete();
        });
    }

    public function getFileUrlAttribute()
    {
        $media = "";
        if(Storage::disk('public')->exists($this->file_path)){
            $media = asset('storage/'.$this->file_path);
        }
        return $media;
    }

    /**
     * Get all of the models that own uploads.
     */
    public function uploadsable()
    {
        return $this->morphTo();
    }
}
