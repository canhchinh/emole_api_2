<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface CareerRepository.
 *
 * @package namespace App\Repositories;
 */
interface CareerRepository extends RepositoryInterface
{
    /**
     * @return mixed
     */
    public function query();

    /**
     * @param $columns
     * @return mixed
     */
    public function select($columns);

    public function sendToUser();
}
