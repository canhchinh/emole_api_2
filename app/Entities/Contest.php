<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Contest.
 *
 * @package namespace App\Entities;
 */
class Contest extends Model implements Transformable
{
    use TransformableTrait, HasFactory;
    protected $table = 'contests';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public $incrementing = true;
    protected $fillable = [
        'id',
        'title',
        'description',
        'image',
        'url',
    ];
}