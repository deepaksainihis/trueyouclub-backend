<?php

namespace App\Models;

use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Education extends Model
{
    use HasFactory,SoftDeletes,Sluggable;

    public $table = 'educations';
    
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'title',
        'slug',
        'description',
        'video_link',
        'video_type',
        'category_id',
        'status',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (Education $model) {
            $model->slug = Str::slug($model->title) . '-' . bin2hex(random_bytes(10));
            $model->created_by = auth()->user()->id;
        });

        static::updating(function (Education $model) {
            if ($model->isDirty('title')) {
                $model->slug = Str::slug($model->title) . '-' . bin2hex(random_bytes(10));
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

    // Featured Image 
    public function featuredImage()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('type', 'education');
    }

    public function getFeaturedImageUrlAttribute()
    {
        if ($this->featuredImage) {
            return $this->featuredImage->file_url;
        }
        return "";
    }

    // Video 

    public function educationVideo()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('type', 'education-video');
    }

    public function getEducationVideoUrlAttribute()
    {
        if ($this->educationVideo) {
            return $this->educationVideo->file_url;
        }
        return "";
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
