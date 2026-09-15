<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use SoftDeletes;

    public $table = 'sections';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'page_id',
        'title',
        'section_key',
        'content_text',
        'other_details',
        'is_image',
        'is_multiple_image',
        'is_video',
        'button',
        'status',
    ];

    public function uploads()
    {
        return $this->morphMany(Uploads::class, 'uploadsable');
    }

    public function sectionImage()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('type', 'page-section-image');
    }

    public function getImageUrlAttribute()
    {
        if ($this->sectionImage) {
            return $this->sectionImage->file_url;
        }
        return "";
    }
}
