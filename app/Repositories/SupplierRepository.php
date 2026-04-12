<?php

namespace App\Repositories;

use App\Models\Layers;
use App\Models\Layup;
use App\Models\Supplier;
use App\Models\User;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class SupplierRepository implements SupplierRepositoryInterface
{
    public function __construct(protected Supplier $model) {}

    public function getAll(User $user): LengthAwarePaginator
    {
        $query = $this->model->oldest();

        if (! $user->isAdmin()) {
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

    public function findWithLayupsAndLayers(int $supplierId): Supplier
    {
        return $this->model->with([
            'layups.layers' => fn ($query) => $query->orderBy('layer_order'),
        ])->findOrFail($supplierId);
    }

    public function upsertLayup(Supplier $supplier, array $data): Layup
    {
        if (! empty($data['id'])) {
            $existing = $supplier->layups()->whereKey($data['id'])->first();

            if (! $existing) {
                throw ValidationException::withMessages([
                    'layups' => ["Layup ID {$data['id']} tidak ditemukan pada supplier ini."],
                ]);
            }

            $existing->update([
                'name' => $data['name'],
            ]);

            return $existing->fresh();
        }

        $layup = $supplier->layups()->firstOrNew([
            'name' => $data['name'],
        ]);

        $layup->fill([
            'name' => $data['name'],
        ])->save();

        return $layup->fresh();
    }

    public function upsertLayer(Layup $layup, array $data): Layers
    {
        if (! empty($data['id'])) {
            $existing = $layup->layers()->whereKey($data['id'])->first();

            if (! $existing) {
                throw ValidationException::withMessages([
                    'layers' => ["Layer ID {$data['id']} tidak ditemukan pada layup ini."],
                ]);
            }

            $existing->update([
                'layer_order' => $data['layer_order'],
                'thickness' => $data['thickness'],
                'width' => $data['width'],
                'angle' => $data['angle'],
            ]);

            return $existing->fresh();
        }

        $layer = $layup->layers()->firstOrNew([
            'layer_order' => $data['layer_order'],
        ]);

        $layer->fill([
            'layer_order' => $data['layer_order'],
            'thickness' => $data['thickness'],
            'width' => $data['width'],
            'angle' => $data['angle'],
        ])->save();

        return $layer->fresh();
    }
}
