<?php

namespace App\Repositories;

use App\Models\SaleItem;
use App\Repositories\Contracts\SaleItemsRepositoryInterface;

class SaleItemsRepository extends BaseRepository implements SaleItemsRepositoryInterface
{
    /**
     * HotelRepository constructor.
     */
    public function __construct(SaleItem $model)
    {
        parent::__construct($model);
    }
}
