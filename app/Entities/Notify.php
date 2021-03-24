<?php

namespace App\Entities;

use App\Entities\Traits\Base;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Job.
 *
 * @package namespace App\Entities;
 */
class Notify extends Model implements Transformable
{
    use TransformableTrait, HasFactory, Base;

    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';

    protected $table = 'notify';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public $incrementing = true;
    protected $fillable = [
        'id',
        'career_id',
        'delivery_name'
    ];

}
