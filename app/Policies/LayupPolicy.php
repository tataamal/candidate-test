<?php

namespace App\Policies;

use App\Models\Layups;
use App\Models\User;

class LayupPolicy
{
    /**
     * Admin bypass semua policy
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) {
            return true;
        }
        return null;
    }

    /**
     * Semua role bisa lihat list layup
     * Supplier hanya lihat miliknya — difilter di controller
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Supplier hanya bisa lihat layup miliknya
     */
    public function view(User $user, Layups $layup): bool
    {
        if ($user->isSupplier()) {
            return $user->supplier?->id === $layup->supplier_id;
        }
        return true; // role user bisa lihat semua
    }

    /**
     * Supplier hanya bisa create layup miliknya
     */
    public function create(User $user): bool
    {
        return $user->isSupplier();
    }

    /**
     * Supplier hanya bisa update layup miliknya
     */
    public function update(User $user, Layups $layup): bool
    {
        if ($user->isSupplier()) {
            return $user->supplier?->id === $layup->supplier_id;
        }
        return false;
    }

    /**
     * Supplier hanya bisa delete layup miliknya
     */
    public function delete(User $user, Layups $layup): bool
    {
        if ($user->isSupplier()) {
            return $user->supplier?->id === $layup->supplier_id;
        }
        return false;
    }
}