<?php

namespace App\Policies;

use App\Models\Layers;
use App\Models\User;

class LayerPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) return true;
        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Layers $layer): bool
    {
        if ($user->isSupplier()) {
            return $user->supplier?->id === $layer->layup->supplier_id;
        }
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSupplier();
    }

    public function update(User $user, Layers $layer): bool
    {
        if ($user->isSupplier()) {
            return $user->supplier?->id === $layer->layup->supplier_id;
        }
        return false;
    }

    public function delete(User $user, Layers $layer): bool
    {
        if ($user->isSupplier()) {
            return $user->supplier?->id === $layer->layup->supplier_id;
        }
        return false;
    }
}