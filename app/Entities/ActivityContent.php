<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Career.
 *
 * @package namespace App\Entities;
 */
class ActivityContent extends Model implements Transformable
{
    use TransformableTrait, HasFactory;
    protected $table = 'activity_contents';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public $incrementing = true;
    protected $fillable = [
        'id',
        'career_id',
        'key',
        'title'
    ];
}
