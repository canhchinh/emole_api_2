<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Sns.
 *
 * @package namespace App\Entities;
 */
class UserWork extends Model implements Transformable
{
    use TransformableTrait, HasFactory;
    protected $table = 'user_works';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public $incrementing = true;
    protected $fillable = [
        'id',
        'user_id',
        'work_id',
        'email',
        'phone',
        'make_up',
        'transportation_cost',
        'post_media',
    ];
}