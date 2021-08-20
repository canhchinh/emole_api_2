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
class Work extends Model implements Transformable
{
    use TransformableTrait, HasFactory;
    protected $table = 'works';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public $incrementing = true;
    protected $fillable = [
        'id',
        'title',
        'job',
        'image',
        'date',
        'slary',
        'place',
        'content',
    ];
}