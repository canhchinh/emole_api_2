<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface PortfolioRepository.
 *
 * @package namespace App\Repositories;
 */
interface PortfolioRepository extends RepositoryInterface
{
    public function paginateQuery(Request $request, $status, $search, $categories);
}
