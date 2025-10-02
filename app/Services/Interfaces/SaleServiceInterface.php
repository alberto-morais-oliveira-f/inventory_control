<?php

namespace App\Services\Interfaces;

use App\Models\Sale;

interface SaleServiceInterface
{
    public function register(array $data): Sale;

    public function calculateTotals(array $items, array $productsMap): array;
}
