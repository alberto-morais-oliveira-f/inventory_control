<?php

namespace App\Repositories;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

abstract class BaseRepository implements BaseRepositoryInterface
{
    /** @var Model */
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * @inheritDoc
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * @inheritDoc
     */
    public function getByValuesIn(string $field, array $values): Collection
    {
        return $this->model->whereIn($field, $values)->get();
    }

    /**
     * @inheritDoc
     */
    public function store(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * @inheritDoc
     */
    public function updateById(array $data, int $id): bool
    {
        return $this->model->where('id', $id)
            ->update($data);
    }

    /**
     * @inheritDoc
     */
    public function delete(int $id): bool
    {
        return $this->model->where('id', $id)
            ->delete();
    }

    /**
     * @inheritDoc
     */
    public function getWithRelation(string $relation): Collection
    {
        return $this->model->with($relation)->get();
    }

    /**
     * @inheritDoc
     */
    public function getByIdAndWithRelations(int $id, array $relations): Builder
    {
        return $this->model->where('id', $id)->with($relations);
    }

    /**
     * @inheritDoc
     */
    public function save(array $data): Model
    {
        $where = [];
        $primaryKey = $this->model->getKeyName();
        if (isset($data[$primaryKey]) and ! empty($data[$primaryKey])) {
            $where = [$primaryKey => $data[$primaryKey]];
        }

        return $this->model->updateOrCreate($where, $data);
    }
}
