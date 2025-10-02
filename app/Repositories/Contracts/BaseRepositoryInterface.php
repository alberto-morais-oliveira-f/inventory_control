<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    /**
     * Get by id
     */
    public function getById(int $id): ?Model;

    /**
     * Get all registers
     */
    public function all(): Collection;

    public function getByValuesIn(string $field, array $values): Collection;

    /**
     * @return mixed
     */
    public function store(array $data): Model;

    /**
     * @return mixed
     */
    public function updateById(array $data, int $id): bool;
}
