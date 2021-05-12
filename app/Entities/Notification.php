<?php

namespace App\Entities;

use App\Entities\Traits\Base;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Notification.
 *
 * @package namespace App\Entities;
 */
class Notification extends Model implements Transformable
{
    use TransformableTrait, HasFactory, Base;

    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLIC = 'public';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'career_id',
        'delivery_name',
        'delivery_contents',
        'subject',
        'url'
    ];

    /**
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        return parent::save($options);
    }

    /**
     * @param $career_ids
     * @return $this
     */
    public function setCareerIds($career_ids)
    {
        if (is_array($career_ids)) {
            if (in_array(0, $career_ids)) {
                $this->career_ids = 0;
            } else {
                $this->career_ids = implode(',', $career_ids);
            }
        }

        return $this;
    }
}
