<?php

namespace App\Repositories\Interfaces;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface SupplierRepositoryInterface
{
    public function getAll(User $user): LengthAwarePaginator;
    public function create(array $data): Supplier;
    public function update(Supplier $supplier, array $data): Supplier;
    public function delete(Supplier $supplier): bool;
}