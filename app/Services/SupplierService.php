<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\User;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SupplierService
{
    public function __construct(
        protected SupplierRepositoryInterface $repository
    ) {}

    public function getAll(User $user): LengthAwarePaginator
    {
        return $this->repository->getAll($user);
    }

    public function create(array $data): Supplier
    {
        return DB::transaction(function () use ($data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make($data['password'] ?? Str::random(12)),
                    'role'     => 'supplier',
                ]
            );
            return $this->repository->create([
                'name'    => $data['name'],
                'user_id' => $user->id,
            ]);
        });
    }
    public function update(Supplier $supplier, array $data): Supplier
    {
        return $this->repository->update($supplier, $data);
    }

    public function delete(Supplier $supplier): bool
    {
        return $this->repository->delete($supplier);
    }
}
