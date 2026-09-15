<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Sluggable;

class Post extends Model
{
    use HasFactory, SoftDeletes, Sluggable;
    public $table = 'posts';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'title',
        'slug',
        'content',
        'publish_date',
        'type',
        'status',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (Post $model) {
            $model->slug = Str::slug($model->title) . '-' . bin2hex(random_bytes(10));
            $model->created_by = auth()->user()->id;
        });

        static::updating(function (Post $model) {
            $model->slug = Str::slug($model->title) . '-' . bin2hex(random_bytes(10));
        });

        static::deleting(function ($model) {
            if ($model->blogImage) {
                $uploadImageId = $model->blogImage->id;
                deleteFile($uploadImageId);
            }
        });

        static::deleting(function ($model) {
            if ($model->newsImage) {
                $uploadImageId = $model->newsImage->id;
                deleteFile($uploadImageId);
            }
        });

        static::deleting(function ($model) {
            if ($model->healthImage) {
                $uploadImageId = $model->healthImage->id;
                deleteFile($uploadImageId);
            }
        });

        // static::creating(function (Post $model) {
        //     $model->slug = $model->generateSlug($model->title);
        // });

        // static::updating(function (Post $model) {
        //     $model->slug = $model->generateSlug($model->title);
        // });
    }

    public function uploads()
    {
        return $this->morphMany(Uploads::class, 'uploadsable');
    }

    public function blogImage()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('type', 'blog');
    }

    public function getBlogImageUrlAttribute()
    {
        if ($this->blogImage) {
            return $this->blogImage->file_url;
        }
        return "";
    }


    public function newsImage()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('type', 'news');
    }

    public function getNewsImageUrlAttribute()
    {
        if ($this->newsImage) {
            return $this->newsImage->file_url;
        }
        return "";
    }

    public function healthImage()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('type', 'health');
    }

    public function getHealthImageUrlAttribute()
    {
        if ($this->healthImage) {
            return $this->healthImage->file_url;
        }
        return "";
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function entryptedSlug()
    {
        // return Str::slug($this->title) . '-' . encrypt($this->getAttribute('id'));

        return Str::slug($this->title) . '-' . bin2hex(random_bytes(10));
        
    }
    
}
