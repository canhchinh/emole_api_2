<?php

namespace App\Repositories;

use App\Entities\Notification;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface UserNotificationRepository
 * @package App\Repositories
 */
interface UserNotificationRepository extends RepositoryInterface
{
    public function addNotification(Notification $notification);

    public function removeNotification(Notification $notification);

    public function addNotiForUser($userId, $notiId);

    public function removeNotiForUser($userId, $notiId);

    public function setReadAllForUser($userId, $notifyId);
}