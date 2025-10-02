<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
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

    /**
     * @param  int  $id
     *
     * @return mixed
     */
    public function delete(int $id): bool;

    /**
     * @param  string  $relation
     *
     * @return mixed
     */
    public function getWithRelation(string $relation): Collection;

    /**
     * @param  int  $id
     * @param  array  $relations
     *
     * @return mixed
     */
    public function getByIdAndWithRelations(int $id, array $relations): Builder;

    /**
     * @param  array  $data
     *
     * @return Model
     */
    public function save(array $data): Model;
}
