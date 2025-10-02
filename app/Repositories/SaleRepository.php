<?php

namespace App\Repositories;

use App\Models\Sale;
use App\Repositories\Contracts\SaleRepositoryInterface;

class SaleRepository extends BaseRepository implements SaleRepositoryInterface
{
    /**
     * HotelRepository constructor.
     */
    public function __construct(Sale $model)
    {
        parent::__construct($model);
    }
}
