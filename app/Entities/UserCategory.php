<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class UserCategory.
 *
 * @package namespace App\Entities;
 */
class UserCategory extends Model implements Transformable
{
    use TransformableTrait,HasFactory;
    protected $table = 'user_categories';
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
        'category_id'
    ];
}