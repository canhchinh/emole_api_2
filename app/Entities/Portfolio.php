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
class Portfolio extends Model implements Transformable
{
    use TransformableTrait,HasFactory;

    const PUBLIC_YES = 1;
    const PUBLIC_NO = 0;

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
        'work_description',
        'member_ids',
        'is_public',
        'tags'
    ];

    public function getImageAttribute($value)
    {
        $result = null;
        if(!empty($value)) {
            $value = json_decode($value);
            if(is_array($value)) {
                foreach($value as $item) {
                    $result[] = [
                        'alt' => 'portfolio image',
                        'url' => config('common.app_url'). $item,
                        'path' => $item,
                    ];
                }
            }
        }

        return $result;
    }

    public function getTagsAttribute($value)
    {
        return $value ? explode(":|||:", $value) : [];
    }
}
