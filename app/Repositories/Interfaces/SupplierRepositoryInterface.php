<?php

namespace App\Repositories\Interfaces;

use App\Models\Layers;
use App\Models\Layup;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface SupplierRepositoryInterface
{
    public function getAll(User $user): LengthAwarePaginator;

    public function create(array $data): Supplier;

    public function update(Supplier $supplier, array $data): Supplier;

    public function delete(Supplier $supplier): bool;

    public function findWithLayupsAndLayers(int $supplierId): Supplier;

    public function upsertLayup(Supplier $supplier, array $data): Layup;

    public function upsertLayer(Layup $layup, array $data): Layers;
}
