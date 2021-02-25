<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class UserSns.
 *
 * @package namespace App\Entities;
 */
class UserSns extends Model implements Transformable
{
    use TransformableTrait,HasFactory;
    protected $table = 'user_sns';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public $incrementing = true;
    protected $fillable = [
        'id',
        'user_id',
        'sns_id',
        'content'
    ];
}