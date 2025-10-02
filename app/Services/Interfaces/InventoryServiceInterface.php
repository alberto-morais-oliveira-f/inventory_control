<?php

namespace App\Services\Interfaces;

use App\Exceptions\ProductException;
use App\Models\Inventory;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

interface InventoryServiceInterface
{
    /**
     * @throws Throwable|ProductException
     */
    public function register(array $data): Inventory;

    public function getInventory(): Collection;
}
