<?php

namespace App\Repositories;

use App\Models\Supplier;
use App\Models\User;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierRepository implements SupplierRepositoryInterface
{
    public function __construct(protected Supplier $model) {}

    public function getAll(User $user): LengthAwarePaginator
    {
        $query = $this->model->oldest();

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return $query->paginate(15);
    }

    public function create(array $data): Supplier
    {
        return $this->model->create($data);
    }

    public function update(Supplier $supplier, array $data): Supplier
    {
        $supplier->update($data);
        return $supplier->fresh();
    }

    public function delete(Supplier $supplier): bool
    {
        return $supplier->delete();
    }
}