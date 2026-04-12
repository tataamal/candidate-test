<?php

namespace App\Services;

use App\Models\Supplier;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use Illuminate\Support\Facades\DB;

class SupplierTransferService
{
    public function __construct(
        protected SupplierRepositoryInterface $repository
    ) {}

    public function exportBySupplier(Supplier $supplier): array
    {
        $supplier = $this->repository->findWithLayupsAndLayers($supplier->id);

        return [
            'supplier' => [
                'id' => $supplier->id,
                'name' => $supplier->name,
            ],
            'layups' => $supplier->layups->map(function ($layup) {
                return [
                    'id' => $layup->id,
                    'name' => $layup->name,
                    'layers' => $layup->layers
                        ->sortBy('layer_order')
                        ->values()
                        ->map(fn ($layer) => [
                            'id' => $layer->id,
                            'layer_order' => $layer->layer_order,
                            'thickness' => $layer->thickness,
                            'width' => $layer->width,
                            'angle' => $layer->angle,
                        ])
                        ->toArray(),
                ];
            })->values()->toArray(),
        ];
    }

    public function importBySupplier(Supplier $supplier, array $payload): array
    {
        return DB::transaction(function () use ($supplier, $payload) {
            $layupsProcessed = 0;
            $layersProcessed = 0;

            foreach ($payload['layups'] as $layupData) {
                $layup = $this->repository->upsertLayup($supplier, [
                    'id' => $layupData['id'] ?? null,
                    'name' => $layupData['name'],
                ]);

                $layupsProcessed++;

                foreach ($layupData['layers'] as $layerData) {
                    $this->repository->upsertLayer($layup, [
                        'id' => $layerData['id'] ?? null,
                        'layer_order' => $layerData['layer_order'],
                        'thickness' => $layerData['thickness'],
                        'width' => $layerData['width'],
                        'angle' => $layerData['angle'],
                    ]);

                    $layersProcessed++;
                }
            }

            return [
                'supplier_id' => $supplier->id,
                'layups_processed' => $layupsProcessed,
                'layers_processed' => $layersProcessed,
            ];
        });
    }
}
