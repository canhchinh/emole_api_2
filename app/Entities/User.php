<?php

namespace App\Entities;

use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Entities\ActivityBase;
use App\Entities\Portfolio;

/**
 * Class User.
 *
 * @package namespace App\Entities;
 */
class User extends Authenticatable implements Transformable
{
    use TransformableTrait, HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_name',
        'given_name',
        'email',
        'email_verified_at',
        'password',
        'google_id',
        'remember_token',
        'title',
        'profession',
        'gender',
        'birthday',
        'self_introduction',
        'avatar',
        'register_finish_step',
        'activity_base_id',
        'is_enable_email_notification',
        'provider',
        'provider_id',
        'twitter_user',
        'tiktok_user',
        'instagram_user',
        'youtube_channel'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'pivot'
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function careers()
    {
        return $this->belongsToMany(
            'App\Entities\Career',
            'App\Entities\UserCareer',
            'user_id',
            'career_id',
            'id',
            'id'
        );
    }

    public function activity_base()
    {
        return $this->hasOne(ActivityBase::class, 'id', 'activity_base_id');
    }

    public function images()
    {
        return $this->hasMany(UserImage::class, 'user_id', 'id');
    }

    public function portfolio()
    {
        return $this->hasMany(Portfolio::class, 'user_id', 'id');
    }

    public function search($username)
    {
        return $this->where('user_name', $username)
            ->orWhere('given_name', $username)
            ->first();
    }
}