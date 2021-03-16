<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Education.
 *
 * @package namespace App\Entities;
 */
class Education extends Model implements Transformable
{
    use TransformableTrait,HasFactory;
    protected $table = 'educations';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public $incrementing = true;
    protected $fillable = [
        'id',
        'user_id',
        'title',
        'role',
        'start_date',
        'end_date',
        'is_still_active',
        'description',
        'link'
    ];

}
