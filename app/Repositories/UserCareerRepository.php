<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface UserCareerRepository.
 *
 * @package namespace App\Repositories;
 */
interface UserCareerRepository extends RepositoryInterface
{
    public function getListForUser(int $userId);
}
