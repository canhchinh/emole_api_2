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
class PortfolioMember extends Model implements Transformable
{
    use TransformableTrait,HasFactory;
    protected $table = 'portfolio_member';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'portfolio_id',
        'member_id',
        'role'
    ];

    public function member()
    {
        return $this->hasOne(User::class, 'id', 'member_id');
    }
}
