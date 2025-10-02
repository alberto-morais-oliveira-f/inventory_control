<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductsRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class ProductsRepository extends BaseRepository implements ProductsRepositoryInterface
{
    // Implement methods for Products
    protected Model $model;

    /**
     * HotelRepository constructor.
     */
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }
}
