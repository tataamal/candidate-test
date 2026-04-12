<?php

namespace App\Imports;

use App\Imports\Sheets\LayersSheetImport;
use App\Imports\Sheets\LayupsSheetImport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SupplierTemplateImport implements SkipsUnknownSheets, WithMultipleSheets
{
    protected LayupsSheetImport $layupsSheet;

    protected LayersSheetImport $layersSheet;

    public function __construct()
    {
        $this->layupsSheet = new LayupsSheetImport;
        $this->layersSheet = new LayersSheetImport;
    }

    public function sheets(): array
    {
        return [
            'Layups' => $this->layupsSheet,
            'Layers' => $this->layersSheet,
        ];
    }

    public function onUnknownSheet($sheetName)
    {
        // ignore unknown sheet
    }

    public function layups(): Collection
    {
        return $this->layupsSheet->rows();
    }

    public function layers(): Collection
    {
        return $this->layersSheet->rows();
    }
}
