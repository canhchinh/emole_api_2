<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface NotificationRepository.
 *
 * @package namespace App\Repositories;
 */
interface NotificationRepository extends RepositoryInterface
{
    public function query();
    public function paginateQuery(Request $request, $status, $search);
    public function simplePaginateQuery();
}
