<?php

namespace App\Repositories;

use App\Entities\Notification;
use App\Entities\User;
use App\Entities\UserCareer;
use App\Entities\UserNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class UserNotificationRepositoryEloquent
 * @package App\Repositories
 */
class UserNotificationRepositoryEloquent extends BaseRepository implements UserNotificationRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserNotification::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @param Notification $notification
     * @return bool
     */
    public function addNotification(Notification $notification)
    {
        if (0 == $notification->career_ids) {
            $userData = User::query()
                ->leftJoin('user_notifications as un', 'users.id', '=', 'un.user_id')
                ->select(['users.id as u_user_id', 'un.*'])
                ->get();

            return $this->doAddNotificationForEachUser($userData, $notification->id);
        } else {
            $careerIds = explode(',', $notification->career_ids);
            $userCareers = UserCareer::query()
                ->leftJoin('user_notifications as un', 'user_careers.user_id', '=', 'un.user_id')
                ->whereIn('user_careers.career_id', $careerIds)
                ->select(['user_careers.user_id as u_user_id', 'un.*'])
                ->groupBy('user_careers.user_id')
                ->get();

            return $this->doAddNotificationForEachUser($userCareers, $notification->id);
        }
    }

    /**
     * @param $userAndNotification
     * @param $notificationId
     * @return bool
     */
    public function doAddNotificationForEachUser($userAndNotification, $notificationId)
    {
        /** @var UserNotification $userNotification */
        foreach ($userAndNotification as $userNotification) {
            // Check condition not exists on the user_notification tbl
            if (!$userNotification->id && $userNotification->u_user_id) {
                $model = new UserNotification();
                $model->user_id = $userNotification->u_user_id;
                $model->setNotificationDataForNew($notificationId);
                $model->save();
                continue;
            }

            // Check condition was exists on the user_notification tbl
            if ($userNotification->id && $userNotification->u_user_id) {
                $model = UserNotification::query()->where(['id' => $userNotification->id])->first();
                Log::info($userNotification->u_user_id);
                if ($model) {
                    $model->setNotificationDataForUpdate($notificationId);
                    $model->save();
                }
                continue;
            }
        }

        return true;
    }

    /**
     * @param Notification $notification
     * @return bool
     */
    public function removeNotification(Notification $notification)
    {
        if (0 == $notification->career_ids) {
            $userData = User::query()
                ->leftJoin('user_notifications as un', 'users.id', '=', 'un.user_id')
                ->select(['users.id as u_user_id', 'un.*'])->get();

            return $this->doRemoveNotificationForEachUser($userData, $notification->id);
        } else {
            $careerIds = explode(',', $notification->career_ids);
            $userCareers = UserCareer::query()
                ->leftJoin('user_notifications as un', 'user_careers.user_id', '=', 'un.user_id')
                ->whereIn('user_careers.career_id', $careerIds)
                ->select(['user_careers.user_id as u_user_id', 'un.*'])->get();

            return $this->doRemoveNotificationForEachUser($userCareers, $notification->id);
        }
    }

    /**
     * @param $userAndNotification
     * @param $notificationId
     * @return bool
     */
    public function doRemoveNotificationForEachUser($userAndNotification, $notificationId)
    {
        $userIds = [];
        foreach ($userAndNotification as $item) {
            $userIds [] = $item->u_user_id;
        }

        if (!$userIds) {
            return true;
        }

        $userNotis = UserNotification::query()
            ->whereIn('user_id', $userIds)->get();

        /** @var UserNotification $noti */
        foreach ($userNotis as $noti) {
            $noti->setNotificationDataForRemove($notificationId);
            $noti->save();
        }

        return true;
    }

    public function addNotiForUser($userId, $notiId)
    {
        $noti = $this->where('user_id', $userId)
            ->first();

        if(empty($noti->id)) {
            $data = [
                'notification_id_all' => [$notiId],
                'notification_id_unread' => [$notiId],
                'notification_id_read' => [],
                'notification_id_deleted' => [],
            ];

            $this->create([
                'user_id' => $userId,
                'notification_data' => json_encode($data)
            ]);
        } else {
            $data = json_decode($noti->notification_data, true);

            $data['notification_id_all'] = array_merge($data['notification_id_all'], [$notiId]);
            $data['notification_id_unread'] = array_merge($data['notification_id_unread'], [$notiId]);

            $noti->notification_data = json_encode($data);
            $noti->save();
        }

        return true;
    }

    public function removeNotiForUser($userId, $notiId)
    {
        $noti = $this->where('user_id', $userId)
            ->first();

        if(!empty($noti->id)) {
            $data = json_decode($noti->notification_data, true);

            unset($data['notification_id_all'][$notiId]);
            unset($data['notification_id_unread'][$notiId]);
            unset($data['notification_id_read'][$notiId]);
            unset($data['notification_id_deleted'][$notiId]);

            $noti->notification_data = json_encode($data);
            $noti->save();
        }

        return true;
    }

    public function setReadAllForUser($userId)
    {
        $noti = $this->where('user_id', $userId)
        ->first();

        if(!empty($noti->id)) {
            $data = json_decode($noti->notification_data, true);

            $data['notification_id_read'] = array_merge($data['notification_id_read'], $data['notification_id_unread']);
            $data['notification_id_unread'] = [];

            $noti->notification_data = json_encode($data);
            $noti->save();
        }

        return true;
    }
}
