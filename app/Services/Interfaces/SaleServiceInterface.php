<?php

namespace App\Services\Interfaces;

use App\Models\Sale;
use Illuminate\Database\Eloquent\Collection;

interface SaleServiceInterface
{
    public function register(array $data): Sale;

    public function calculateTotals(array $items, Collection $productsMap): array;

    public function validateStock(array $items): void;
}
