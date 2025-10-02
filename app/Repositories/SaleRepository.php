<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Repositories\Contracts\SaleRepositoryInterface;

class SaleRepository extends BaseRepository implements SaleRepositoryInterface
{
    // Implement methods for Sale
    protected $model;

    /**
     * HotelRepository constructor.
     */
    public function __construct(HotelMpAccount $model)
    {
        $this->model = $model;
    }
}
