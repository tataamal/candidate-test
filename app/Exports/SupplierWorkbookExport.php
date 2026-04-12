<?php

namespace App\Exports;

use App\Exports\Sheets\SupplierInstructionsSheetExport;
use App\Exports\Sheets\SupplierLayersSheetExport;
use App\Exports\Sheets\SupplierLayupsSheetExport;
use App\Exports\Sheets\SupplierSummarySheetExport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SupplierWorkbookExport implements WithMultipleSheets
{
    public function __construct(
        protected array $payload
    ) {}

    public function sheets(): array
    {
        return [
            new SupplierSummarySheetExport($this->payload),
            new SupplierLayupsSheetExport($this->payload),
            new SupplierLayersSheetExport($this->payload),
            new SupplierInstructionsSheetExport(false),
        ];
    }
}
