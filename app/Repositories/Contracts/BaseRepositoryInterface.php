<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

interface BaseRepositoryInterface
{
    /**
     * Get by id
     *
     * @param  int  $id
     *
     * @return Model|null
     */
    public function getById(int $id): ?Model;

    /**
     * Get all registers
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * @param  string  $field
     * @param  array  $values
     *
     * @return Collection
     */
    public function getByValuesIn(string $field, array $values): Collection;

    /**
     * @param  array  $data
     *
     * @return mixed
     */
    public function store(array $data): Model;

    /**
     * @param  array  $data
     * @param  int  $id
     *
     * @return mixed
     */
    public function updateById(array $data, int $id): bool;
}
