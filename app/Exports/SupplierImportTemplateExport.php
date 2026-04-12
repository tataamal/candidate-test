<?php

namespace App\Exports;

use App\Exports\Sheets\SupplierInstructionsSheetExport;
use App\Exports\Sheets\SupplierTemplateIntroSheetExport;
use App\Exports\Sheets\SupplierTemplateLayersSheetExport;
use App\Exports\Sheets\SupplierTemplateLayupsSheetExport;
use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SupplierImportTemplateExport implements WithMultipleSheets
{
    public function __construct(
        protected Supplier $supplier
    ) {}

    public function sheets(): array
    {
        return [
            new SupplierTemplateIntroSheetExport($this->supplier),
            new SupplierTemplateLayupsSheetExport,
            new SupplierTemplateLayersSheetExport,
            new SupplierInstructionsSheetExport(true),
        ];
    }
}
