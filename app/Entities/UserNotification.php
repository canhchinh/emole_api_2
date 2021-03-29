<?php

namespace App\Entities;

use App\Entities\Traits\Base;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class UserNotification.
 *
 * @package namespace App\Entities;
 */
class UserNotification extends Model implements Transformable
{
    use Base;
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * @param $notificationId
     * @return $this
     */
    public function setNotificationDataForNew($notificationId) {
        $data = [
            'notification_id_all' => [$notificationId],
            'notification_id_unread' => [$notificationId],
            'notification_id_read' => [],
            'notification_id_deleted' => [],
        ];

        return $this->setNotificationData($data);
    }

    /**
     * @param $notificationId
     * @return $this
     */
    public function setNotificationDataForUpdate($notificationId)
    {
        $notificationData = $this->getNotificationData();
        $data['notification_id_all']        = array_merge($notificationData->notification_id_all, [$notificationId]);
        $data['notification_id_unread']     = array_merge($notificationData->notification_id_unread, [$notificationId]);
        $data['notification_id_read']       = $notificationData->notification_id_read;
        $data['notification_id_deleted']    = $notificationData->notification_id_read;

        return $this->setNotificationData($data);
    }

    /**
     * @param $data
     * @return $this
     */
    public function setNotificationData($data)
    {
        $this->notification_data = json_encode($data);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNotificationData()
    {
        return json_decode($this->notification_data);
    }

}
