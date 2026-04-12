<?php

namespace App\Services;

use App\Imports\SupplierTemplateImport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class SupplierExcelImportService
{
    public function payloadFromFile(UploadedFile $file): array
    {
        $import = new SupplierTemplateImport;

        Excel::import($import, $file);

        $layupRows = $import->layups();
        $layerRows = $import->layers();

        if ($layupRows->isEmpty() && $layerRows->isEmpty()) {
            throw ValidationException::withMessages([
                'file' => ['Sheet Layups dan Layers kosong.'],
            ]);
        }

        $layupMap = $this->buildLayupMap($layupRows);
        $this->attachLayers($layupMap, $layerRows);

        return [
            'layups' => array_values($layupMap),
        ];
    }

    protected function buildLayupMap(Collection $layupRows): array
    {
        $map = [];

        foreach ($layupRows as $index => $row) {
            if ($this->isEmptyRow($row)) {
                continue;
            }

            $name = trim((string) ($row['layup_name'] ?? ''));
            $id = $this->nullableInt($row['layup_id'] ?? null);

            if ($name === '' && ! $id) {
                throw ValidationException::withMessages([
                    'file' => ['Sheet Layups baris '.($index + 2).': layup_name wajib diisi jika layup_id kosong.'],
                ]);
            }

            $key = $id ? 'id:'.$id : 'name:'.mb_strtolower($name);

            $map[$key] = array_filter([
                'id' => $id,
                'name' => $name,
                'layers' => $map[$key]['layers'] ?? [],
            ], fn ($value) => ! is_null($value));
        }

        return $map;
    }

    protected function attachLayers(array &$layupMap, Collection $layerRows): void
    {
        foreach ($layerRows as $index => $row) {
            if ($this->isEmptyRow($row)) {
                continue;
            }

            $validator = Validator::make($row->toArray(), [
                'layer_order' => ['required', 'integer', 'min:1'],
                'thickness' => ['required', 'numeric', 'min:0'],
                'width' => ['required', 'numeric', 'min:0'],
                'angle' => ['required', 'numeric'],
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages([
                    'file' => ['Sheet Layers baris '.($index + 2).': '.$validator->errors()->first()],
                ]);
            }

            $layupId = $this->nullableInt($row['layup_id'] ?? null);
            $layupName = trim((string) ($row['layup_name'] ?? ''));
            $layerId = $this->nullableInt($row['layer_id'] ?? null);

            if (! $layupId && $layupName === '') {
                throw ValidationException::withMessages([
                    'file' => ['Sheet Layers baris '.($index + 2).': layup_id atau layup_name wajib diisi.'],
                ]);
            }

            $key = $layupId ? 'id:'.$layupId : 'name:'.mb_strtolower($layupName);

            if (! isset($layupMap[$key])) {
                $layupMap[$key] = array_filter([
                    'id' => $layupId,
                    'name' => $layupName,
                    'layers' => [],
                ], fn ($value) => ! is_null($value));
            }

            $layupMap[$key]['layers'][] = array_filter([
                'id' => $layerId,
                'layer_order' => (int) $row['layer_order'],
                'thickness' => (float) $row['thickness'],
                'width' => (float) $row['width'],
                'angle' => (float) $row['angle'],
            ], fn ($value) => ! is_null($value));
        }
    }

    protected function isEmptyRow($row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    protected function nullableInt($value): ?int
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return (int) $value;
    }
}
