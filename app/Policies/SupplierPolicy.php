<?php

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;

class SupplierPolicy
{
    /**
     * Admin bypass semua policy
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true; // admin selalu boleh
        }

        return null; // lanjut cek policy di bawah
    }

    /**
     * Semua role bisa lihat list supplier
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Supplier & user hanya bisa lihat supplier miliknya sendiri.
     * Admin sudah di-bypass via before().
     */
    public function view(User $user, Supplier $supplier): bool
    {
        return $supplier->user_id === $user->id;
    }

    /**
     * Hanya admin yang bisa create (sudah di-handle before())
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Hanya admin yang bisa update (sudah di-handle before())
     */
    public function update(User $user, Supplier $supplier): bool
    {
        return false;
    }

    /**
     * Hanya admin yang bisa delete (sudah di-handle before())
     */
    public function delete(User $user, Supplier $supplier): bool
    {
        return false;
    }

    public function export(User $user, Supplier $supplier): bool
    {
        return $user->isSupplier() && $supplier->user_id === $user->id;
    }

    public function import(User $user, Supplier $supplier): bool
    {
        return $user->isSupplier() && $supplier->user_id === $user->id;
    }
}
