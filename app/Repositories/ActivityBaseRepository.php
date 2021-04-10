<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface CareerRepository.
 *
 * @package namespace App\Repositories;
 */
interface ActivityBaseRepository extends RepositoryInterface
{
    public function query();
}
