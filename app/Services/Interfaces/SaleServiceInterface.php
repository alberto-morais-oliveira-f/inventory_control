<?php

namespace App\Services\Interfaces;

use App\Models\Sale;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface SaleServiceInterface
{
    public function register(array $data): Sale;

    public function calculateTotals(array $items, Collection $productsMap): array;

    public function validateStock(array $items): void;

    public function getSalesReport(array $filters, int $perPage = 15): LengthAwarePaginator;
}
