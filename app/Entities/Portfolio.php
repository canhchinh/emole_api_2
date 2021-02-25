<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Portfolio.
 *
 * @package namespace App\Entities;
 */
class Portfolio extends Model implements Transformable
{
    use TransformableTrait,HasFactory;
    protected $table = 'portfolios';
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
        'image',
        'job_description',
        'start_date',
        'end_date',
        'is_still_active',
        'member',
        'budget',
        'reach_number',
        'view_count',
        'like_count',
        'comment_count',
        'cpa_count',
        'video_link',
        'work_link',
        'work_description'
    ];

}