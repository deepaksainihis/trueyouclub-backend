<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmailMail;

class User extends Authenticatable implements MustVerifyEmail
{
    use SoftDeletes, Notifiable, HasApiTokens;

    protected $guard = 'web';

    public $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'stripe_customer_id',
        'first_name',
        'last_name',
        'name',
        'email',
        'password',
        'phone',
        'otp',
        'location_id',
        'register_type',
        'social_id',
        'social_json',
        'remember_token',
        'is_active',
        'vip_at',
        'star_no',
        'booking_id',
        'last_login_at',
        'email_verified_at',
        'phone_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at',
        'email_verified_at',
        'phone_verified_at',
        'last_login_at',
    ];


    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            if ($model->profileImage) {
                $uploadImageId = $model->profileImage->id;
                deleteFile($uploadImageId);
            }
        });
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function uploads()
    {
        return $this->morphMany(Uploads::class, 'uploadsable');
    }


    public function getIsAdminAttribute()
    {
        return $this->roles()->where('id', 1)->exists();
    }

    public function getIsUserAttribute()
    {
        return $this->roles()->where('id', 2)->exists();
    }

    public function getIsVolunteerAttribute()
    {
        return $this->roles()->where('id', 3)->exists();
    }

    public function profileImage()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('type', 'profile');
    }

    public function getProfileImageUrlAttribute()
    {
        if ($this->profileImage) {
            return $this->profileImage->file_url;
        }
        return "";
    }

    public function NotificationSendToVerifyEmail()
    {
        $user = $this;

        $url = config('constants.front_end_url') . 'email/verify/' . $user->id . '/' . sha1($user->email);

        $subject = 'Verify Email Address';

        Mail::to($user->email)->queue(new VerifyEmailMail($user->name, $url, $subject));
    }
    
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function quotes()
    {
        return $this->belongsToMany(Quote::class,'quote_user')->withPivot(['status','created_at']);
    }

    public function availabilities(){
        return $this->hasMany(VolunteerAvailability::class,'volunteer_id');
    }

    public function userLocation(){
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }
}
