<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Entities\Career;

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
        'tags',
        'setting',
    ];
    protected $hidden = [
        'pivot'
    ];

    public function getSettingAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getTagsAttribute($value)
    {
        return $value ? explode(":|||:", $value) : [];
    }
}
