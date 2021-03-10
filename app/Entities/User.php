<?php

namespace App\Entities;

use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Entities\ActivityBase;

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
        'remember_token',
        'title',
        'profession',
        'gender',
        'birthday',
        'self_introduction',
        'avatar',
        'register_finish_step',
        'activity_base_id',
        'is_enable_email_notification'
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
            'career_id',
            'user_id',
            'id',
            'id'
        );
    }

    public function activity_base()
    {
        return $this->hasOne(ActivityBase::class, 'id', 'activity_base_id');
    }
}
