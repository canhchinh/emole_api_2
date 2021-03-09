<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Career.
 *
 * @package namespace App\Entities;
 */
class Career extends Model implements Transformable
{
    use TransformableTrait,HasFactory;
    protected $table = 'careers';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public $incrementing = true;
    protected $fillable = [
        'id',
        'title'
    ];
    protected $hidden = [
        'pivot'
    ];
}
