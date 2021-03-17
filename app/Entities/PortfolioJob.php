<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Entities\User;

/**
 * Class Portfolio.
 *
 * @package namespace App\Entities;
 */
class PortfolioJob extends Model implements Transformable
{
    use TransformableTrait,HasFactory;
    protected $table = 'portfolio_job';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_id',
        'portfolio_id',
        'job_id'
    ];
}

