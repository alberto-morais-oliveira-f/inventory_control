<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface InventoryRepositoryInterface extends BaseRepositoryInterface
{
    public function list(): Collection;

    public function countItem($productId): ?int;
}
