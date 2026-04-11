<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSuppliersRequest;
use App\Http\Requests\UpdateSuppliersRequest;
use App\Http\Resources\SupplierResources;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\JsonResponse;

/**
 * @group Supplier Management
 *
 * API endpoints untuk mengelola data supplier.
 */
class SupplierController extends Controller
{
    public function __construct(
        protected SupplierService $service
    ) {}

    /**
     * List semua supplier
     *
     * @queryParam page int Nomor halaman. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "data": [{"id": 1, "name": "PT Graha Kalasta", "created_at": "2024-01-01 00:00:00"}],
     *   "meta": {"total": 1, "per_page": 15, "current_page": 1, "last_page": 1}
     * }
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Supplier::class);

        $suppliers = $this->service->getAll();

        return response()->json([
            'success' => true,
            'data'    => SupplierResources::collection($suppliers),
            'meta'    => [
                'current_page' => $suppliers->currentPage(),
                'last_page'    => $suppliers->lastPage(),
                'per_page'     => $suppliers->perPage(),
                'total'        => $suppliers->total(),
            ],
        ]);
    }

    /**
     * Tambah supplier baru
     *
     * @bodyParam name string required Nama supplier. Example: PT Graha Kalasta
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Supplier created successfully.",
     *   "data": {"id": 1, "name": "PT Graha Kalasta", "created_at": "2024-01-01 00:00:00"}
     * }
     * @response 403 {"message": "This action is unauthorized."}
     */
    public function store(StoreSuppliersRequest $request): JsonResponse
    {
        $this->authorize('create', Supplier::class);

        $supplier = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Supplier created successfully.',
            'data'    => new SupplierResources($supplier),
        ], 201);
    }

    /**
     * Detail supplier
     *
     * @response 200 {
     *   "success": true,
     *   "data": {"id": 1, "name": "PT Graha Kalasta", "created_at": "2024-01-01 00:00:00"}
     * }
     * @response 404 {"message": "No query results for model [Supplier]."}
     */
    public function show(Supplier $supplier): JsonResponse
    {
        $this->authorize('view', $supplier);

        return response()->json([
            'success' => true,
            'data'    => new SupplierResources($supplier),
        ]);
    }

    /**
     * Update supplier
     *
     * @bodyParam name string Nama supplier. Example: PT Sejahtera
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Supplier updated successfully.",
     *   "data": {"id": 1, "name": "PT Sejahtera", "created_at": "2024-01-01 00:00:00"}
     * }
     * @response 403 {"message": "This action is unauthorized."}
     * @response 404 {"message": "No query results for model [Supplier]."}
     */
    public function update(UpdateSuppliersRequest $request, Supplier $supplier): JsonResponse
    {
        $this->authorize('update', $supplier);

        $supplier = $this->service->update($supplier, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Supplier updated successfully.',
            'data'    => new SupplierResources($supplier),
        ]);
    }

    /**
     * Hapus supplier
     *
     * @response 200 {"success": true, "message": "Supplier deleted successfully."}
     * @response 403 {"message": "This action is unauthorized."}
     * @response 404 {"message": "No query results for model [Supplier]."}
     */
    public function destroy(Supplier $supplier): JsonResponse
    {
        $this->authorize('delete', $supplier);

        $this->service->delete($supplier);

        return response()->json([
            'success' => true,
            'message' => 'Supplier deleted successfully.',
        ]);
    }
}