<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class UserCareer.
 *
 * @package namespace App\Entities;
 */
class UserCareer extends Model implements Transformable
{
    use TransformableTrait,HasFactory;
    protected $table = 'user_careers';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $fillable = [
        'id',
        'user_id',
        'career_id',
        'tag',
        'category_ids',
        'job_ids',
        'genre_ids'
    ];
}
