<?php

namespace App\Services\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface ProductServiceInterface
{
    public function register(array $data): Model;

    public function updateById(array $data, int $id): bool;
}
