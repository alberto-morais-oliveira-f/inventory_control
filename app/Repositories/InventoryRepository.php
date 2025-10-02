<?php

namespace App\Repositories;

use App\Models\Inventory;
use App\Repositories\Contracts\InventoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InventoryRepository extends BaseRepository implements InventoryRepositoryInterface
{
    // Implement methods for Inventory
    protected Model $model;

    /**
     * HotelRepository constructor.
     */
    public function __construct(Inventory $model)
    {
        parent::__construct($model);
    }

    public function list(): Collection
    {
        return $this->model->query()
            ->join('products as p', 'p.id', '=', 'inventory.product_id')
            ->select([
                'p.id as product_id',
                'p.sku',
                'p.name',
                DB::raw('SUM(inventory.quantity) as total_quantity'),
                DB::raw('SUM(inventory.quantity * p.cost_price) as total_cost'),
                DB::raw('SUM(inventory.quantity * p.sale_price) as total_sale'),
                DB::raw('(SUM(inventory.quantity * p.sale_price) - SUM(inventory.quantity * p.cost_price)) as projected_profit')
            ])
            ->groupBy('p.id') // só id, mais performático
            ->orderBy('p.name')
            ->get();
    }

    public function countItem($productId): ?int
    {
        return $this->model::where('product_id', $productId)
            ->sum('quantity');
    }
}
