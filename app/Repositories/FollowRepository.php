<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface FollowRepository.
 *
 * @package namespace App\Repositories;
 */
interface FollowRepository extends RepositoryInterface
{
    public function getListFollowByUser(int $userId);
}
