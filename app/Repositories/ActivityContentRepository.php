<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface CareerRepository.
 *
 * @package namespace App\Repositories;
 */
interface ActivityContentRepository extends RepositoryInterface
{
    public function getFreshCareer(int $careerId);

    public function query();
}
