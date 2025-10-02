<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface SaleRepositoryInterface extends BaseRepositoryInterface
{
    public function getSalesReport(array $filters, int $perPage = 15): LengthAwarePaginator;
}
