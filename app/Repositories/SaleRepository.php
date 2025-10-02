<?php

namespace App\Repositories;

use App\Models\Sale;
use App\Repositories\Contracts\SaleRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class SaleRepository extends BaseRepository implements SaleRepositoryInterface
{
    /**
     * HotelRepository constructor.
     */
    public function __construct(Sale $model)
    {
        parent::__construct($model);
    }

    public function getSalesReport(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Sale::query()
            ->with(['items.product']); // já carrega os produtos

        if (! empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        if (! empty($filters['product_sku'])) {
            $sku = $filters['product_sku'];
            $query->whereHas('items.product', fn ($q) => $q->where('sku', $sku));
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->through(function (Sale $sale) {
                return [
                    'id' => $sale->id,
                    'status' => $sale->status,
                    'total_amount' => $sale->total_amount,
                    'total_cost' => $sale->total_cost,
                    'total_profit' => $sale->total_profit,
                    'created_at' => $sale->created_at->toDateTimeString(),
                    'items' => $sale->items->map(fn ($item) => [
                        'product_id' => $item->product_id,
                        'sku' => $item->product->sku ?? null,
                        'name' => $item->product->name ?? null,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'unit_cost' => $item->unit_cost,
                    ])->toArray(),
                ];
            });
    }
}
