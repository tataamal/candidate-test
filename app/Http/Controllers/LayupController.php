<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLayupRequest;
use App\Http\Requests\UpdateLayupRequest;
use App\Http\Resources\LayupResource;
use App\Models\Layup;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;

/**
 * @group Layup Management
 *
 * API endpoints untuk mengelola data layup milik supplier.
 */
class LayupController extends Controller
{
    /**
     * List semua layup milik supplier
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "supplier_id": 1,
     *       "name": "Layup A",
     *       "created_at": "2024-01-01 00:00:00",
     *      "updated_at": "2024-01-01 00:00:00"
     *     }
     *   ]
     * }
     */
    public function index(Supplier $supplier): JsonResponse
    {
        $Layup = $supplier->layups()->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'data' => LayupResource::collection($Layup),
            'meta' => [
                'total' => $Layup->total(),
                'per_page' => $Layup->perPage(),
                'current_page' => $Layup->currentPage(),
                'last_page' => $Layup->lastPage(),
            ],
        ]);
    }

    /**
     * Tambah layup baru ke supplier
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Layup created successfully.",
     *   "data": {
     *     "id": 1,
     *     "supplier_id": 1,
     *     "name": "Layup A",
     *     "created_at": "2024-01-01 00:00:00",
     *     "updated_at": "2024-01-01 00:00:00"
     *   }
     * }
     */
    public function store(StoreLayupRequest $request, Supplier $supplier): JsonResponse
    {
        $layup = $supplier->layups()->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Layup created successfully.',
            'data' => new LayupResource($layup),
        ], 201);
    }

    /**
     * Detail layup
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "supplier_id": 1,
     *     "name": "Layup A",
     *     "created_at": "2024-01-01 00:00:00"
     *     "updated_at": "2024-01-01 00:00:00"
     *   }
     * }
     * @response 404 {
     *   "message": "No query results for model [Layup]."
     * }
     */
    public function show(Supplier $supplier, Layup $layup): JsonResponse
    {
        $this->authorizeLayup($supplier, $layup);

        return response()->json([
            'success' => true,
            'data' => new LayupResource($layup),
        ]);
    }

    /**
     * Update layup
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Layup updated successfully.",
     *   "data": {
     *     "id": 1,
     *     "supplier_id": 1,
     *     "name": "Layup B",
     *     "created_at": "2024-01-01 00:00:00",
     *     "updated_at": "2024-01-01 00:00:00"
     *   }
     * }
     */
    public function update(UpdateLayupRequest $request, Supplier $supplier, Layup $layup): JsonResponse
    {
        $this->authorizeLayup($supplier, $layup);

        $layup->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Layup updated successfully.',
            'data' => new LayupResource($layup),
        ]);
    }

    /**
     * Hapus layup
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Layup deleted successfully."
     * }
     */
    public function destroy(Supplier $supplier, Layup $layup): JsonResponse
    {
        $this->authorizeLayup($supplier, $layup);

        $layup->delete();

        return response()->json([
            'success' => true,
            'message' => 'Layup deleted successfully.',
        ]);
    }

    /**
     * Pastikan layup memang milik supplier yang dimaksud
     */
    private function authorizeLayup(Supplier $supplier, Layup $layup): void
    {
        abort_if(
            $layup->supplier_id !== $supplier->id,
            403,
            'Layup does not belong to this supplier.'
        );
    }
}
