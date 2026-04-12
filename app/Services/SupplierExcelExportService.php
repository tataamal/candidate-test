<?php

namespace App\Services;

use App\Exports\SupplierImportTemplateExport;
use App\Exports\SupplierWorkbookExport;
use App\Models\Supplier;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SupplierExcelExportService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected SupplierTransferService $transferService
    ) {}

    public function downloadBySupplier(Supplier $supplier): BinaryFileResponse
    {
        $payload = $this->transferService->exportBySupplier($supplier);

        $filename = Str::slug($payload['supplier']['name'] ?: 'supplier')
            .'-layup-export-'
            .now()->format('Ymd_His')
            .'.xlsx';

        return Excel::download(new SupplierWorkbookExport($payload), $filename);
    }

    public function downloadTemplate(Supplier $supplier): BinaryFileResponse
    {
        $filename = 'supplier-import-template-'.$supplier->id.'.xlsx';

        return Excel::download(new SupplierImportTemplateExport($supplier), $filename);
    }
}
