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
        'delivery_name'
    ];

    public function save(array $options = [])
    {
        if (!$this->exists && $this->status == self::STATUS_PUBLIC) {
            $this->sendToUserNotification();
        }

        parent::save();
    }

    public function sendToUserNotification()
    {
        $userNotification = new UserNotification();

    }

    public function userNotification()
    {
        return $this->belongsTo('App\User');
    }
}
