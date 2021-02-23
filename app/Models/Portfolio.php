<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;
    protected $table = 'portfolio';
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
