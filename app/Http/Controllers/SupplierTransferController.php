<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportSupplierFileRequest;
use App\Models\Supplier;
use App\Services\SupplierExcelExportService;
use App\Services\SupplierExcelImportService;
use App\Services\SupplierTransferService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @group Supplier Import Export
 *
 * API endpoints untuk export dan import data supplier beserta layup dan layers.
 */
class SupplierTransferController extends Controller
{
    public function __construct(
        protected SupplierTransferService $service,
        protected SupplierExcelExportService $excelExportService,
        protected SupplierExcelImportService $excelImportService,
    ) {}

    /**
     * Export data supplier ke Excel
     *
     * @authenticated
     *
     * @urlParam supplier integer required ID supplier. Example: 1
     */
    public function export(Supplier $supplier): BinaryFileResponse
    {
        $this->authorize('export', $supplier);

        return $this->excelExportService->downloadBySupplier($supplier);
    }

    /**
     * Download template import Excel
     *
     * @authenticated
     *
     * @urlParam supplier integer required ID supplier. Example: 1
     */
    public function template(Supplier $supplier): BinaryFileResponse
    {
        $this->authorize('import', $supplier);

        return $this->excelExportService->downloadTemplate($supplier);
    }

    /**
     * Import data supplier dari Excel template
     *
     * @authenticated
     *
     * @urlParam supplier integer required ID supplier. Example: 1
     *
     * @bodyParam file file required File Excel template (.xlsx / .xls).
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Import berhasil.",
     *   "data": {
     *     "supplier_id": 1,
     *     "layups_processed": 2,
     *     "layers_processed": 3
     *   }
     * }
     */
    public function import(ImportSupplierFileRequest $request, Supplier $supplier): JsonResponse
    {
        $this->authorize('import', $supplier);

        $payload = $this->excelImportService->payloadFromFile($request->file('file'));

        $result = $this->service->importBySupplier($supplier, $payload);

        return response()->json([
            'success' => true,
            'message' => 'Import berhasil.',
            'data' => $result,
        ]);
    }
}
