<?php

namespace App\Models;

use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory,SoftDeletes,Sluggable;
    public $table = 'categories';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',       
        'status',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (Category $model) {
            $model->slug = Str::slug($model->name) . '-' . bin2hex(random_bytes(10));
            $model->created_by = auth()->user()->id;
        });

        static::updating(function (Category $model) {
            if ($model->isDirty('name')) {
                $model->slug = Str::slug($model->name) . '-' . bin2hex(random_bytes(10));
            }
        });
       
        static::deleting(function ($model) {
            if ($model->featuredImage) {
                $uploadImageId = $model->featuredImage->id;
                deleteFile($uploadImageId);
            }
        });
    }   

    public function uploads()
    {
        return $this->morphMany(Uploads::class, 'uploadsable');
    }

    public function featuredImage()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('type', 'category');
    }

    public function getFeaturedImageUrlAttribute()
    {
        if ($this->featuredImage) {
            return $this->featuredImage->file_url;
        }
        return "";
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }   

    public function educations()
    {
        return $this->hasMany(Education::class, 'category_id');
    }

}
