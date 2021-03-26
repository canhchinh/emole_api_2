<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class UserImage.
 *
 * @package namespace App\Entities;
 */
class UserImage extends Model implements Transformable
{
    use TransformableTrait,HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public $incrementing = true;
    protected $fillable = [
        'id',
        'user_id',
        'url',
        'created_at',
        'updated_at',
    ];

}
