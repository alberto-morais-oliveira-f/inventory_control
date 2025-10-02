<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Repositories\Contracts\SaleItemsRepositoryInterface;

class SaleItemsRepository extends BaseRepository implements SaleItemsRepositoryInterface
{
    // Implement methods for SaleItems
    protected $model;

    /**
     * HotelRepository constructor.
     */
    public function __construct(HotelMpAccount $model)
    {
        $this->model = $model;
    }
}
