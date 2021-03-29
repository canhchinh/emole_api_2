<?php

namespace App\Repositories;

use App\Entities\Notification;
use App\Entities\User;
use App\Entities\UserCareer;
use App\Entities\UserNotification;
use Illuminate\Support\Facades\DB;
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
                ->select(['users.id as u_user_id', 'un.*'])->get();

            return $this->doAddNotificationForEachUser($userData, $notification->id);
        } else {
            $careerIds = explode(',', $notification->career_ids);
            $userCareers = UserCareer::query()
                ->leftJoin('user_notifications as un', 'user_careers.user_id', '=', 'un.user_id')
                ->whereIn('user_careers.career_id', $careerIds)
                ->select(['user_careers.user_id as u_user_id', 'un.*'])->get();

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
                if ($model) {
                    $model->setNotificationDataForUpdate($notificationId);
                    $model->save();
                }
                continue;
            }
        }

        return true;
    }
}
