<?php

namespace App\Infrastructure;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository
{
    protected abstract function getModelClass(): string;

    public function find(int $id): ?Model
    {
        return $this->getModelClass()::find($id);
    }

    public function create(array $data): Model
    {
        return $this->getModelClass()::create($data);
    }

    public function update(Model $model, array $data): bool
    {
        return $model->update($data);
    }

    public function delete(Model $model): ?bool
    {
        return $model->delete();
    }
} 