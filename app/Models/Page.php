<?php

namespace App\Models;

use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, Sluggable, SoftDeletes;
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'parent_page_id',
        'title',
        'subtitle',
        'slug',
        'button',
        'link',
        'status',
        'created_by'
    ];

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::creating(function (Page $model) {
    //         $model->created_by = auth()->user()->id;
    //         $model->slug = $model->generateSlug($model->page_name);
    //     });

    //     static::updating(function (Page $model) {
    //         $model->slug = $model->generateSlug($model->page_name);
    //     });
    // }

    public function uploads()
    {
        return $this->morphMany(Uploads::class, 'uploadsable');
    }

    public function pageImage()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('type', 'page');
    }

    public function getImageUrlAttribute()
    {
        if ($this->pageImage) {
            return $this->pageImage->file_url;
        }
        return "";
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getEncryptSlugAttribute()
    {
        return Str::slug($this->title) . '-' . encrypt($this->getAttribute('id'));
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }
}
