<?php

namespace App\Services;

use App\Repositories\Contracts\ProductsRepositoryInterface;
use App\Services\Interfaces\ProductServiceInterface;
use Illuminate\Database\Eloquent\Model;

readonly class ProductService implements ProductServiceInterface
{
    public function __construct(private ProductsRepositoryInterface $productsRepository)
    {
    }

    public function register(array $data): Model
    {
        return $this->productsRepository->store($data);
    }

    public function updateById(array $data, int $id): bool
    {
        return $this->productsRepository->updateById($data, $id);
    }
}
