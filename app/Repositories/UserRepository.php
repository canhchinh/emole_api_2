<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface UserRepository.
 *
 * @package namespace App\Repositories;
 */
interface UserRepository extends RepositoryInterface
{
    public function listUsers($userId, $filters = [], $page = 1, $limit = 10, $query);

    public function is_base64($file);

    public function activeAccount($token);

    public function createImageInfo($user);

    public function getListUserWithOrders($idUser);

    // For admin =======================================================================================================

    public function paginateQuery(Request $request, $status, $search);
}