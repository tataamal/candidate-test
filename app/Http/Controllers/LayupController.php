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
     *   "data": [{"id": 1, "supplier_id": 1, "name": "Layup A", "created_at": "2024-01-01 00:00:00"}],
     *   "meta": {"total": 1, "per_page": 10, "current_page": 1, "last_page": 1}
     * }
     */
    public function index(Supplier $supplier): JsonResponse
    {
        $this->authorize('viewAny', Layup::class);

        // Supplier hanya lihat miliknya
        if (auth()->user()->isSupplier()) {
            $Layup = $supplier->Layup()
                ->where('supplier_id', auth()->user()->supplier->id)
                ->latest()
                ->paginate(10);
        } else {
            $Layup = $supplier->Layup()->latest()->paginate(10);
        }

        return response()->json([
            'success' => true,
            'data'    => LayupResource::collection($Layup),
            'meta'    => [
                'total'        => $Layup->total(),
                'per_page'     => $Layup->perPage(),
                'current_page' => $Layup->currentPage(),
                'last_page'    => $Layup->lastPage(),
            ],
        ]);
    }

    /**
     * Tambah layup baru ke supplier
     *
     * @bodyParam name string required Nama layup. Example: Layup A
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Layup created successfully.",
     *   "data": {"id": 1, "supplier_id": 1, "name": "Layup A", "created_at": "2024-01-01 00:00:00"}
     * }
     * @response 403 {"message": "This action is unauthorized."}
     */
    public function store(StoreLayupRequest $request, Supplier $supplier): JsonResponse
    {
        $this->authorize('create', Layup::class);

        // Supplier hanya bisa tambah ke supplier miliknya
        if (auth()->user()->isSupplier()) {
            abort_if(
                auth()->user()->supplier?->id !== $supplier->id,
                403,
                'Anda tidak bisa menambah layup ke supplier lain.'
            );
        }

        $layup = $supplier->Layup()->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Layup created successfully.',
            'data'    => new LayupResource($layup),
        ], 201);
    }

    /**
     * Detail layup
     *
     * @response 200 {
     *   "success": true,
     *   "data": {"id": 1, "supplier_id": 1, "name": "Layup A", "created_at": "2024-01-01 00:00:00"}
     * }
     * @response 403 {"message": "This action is unauthorized."}
     * @response 404 {"message": "No query results for model [Layup]."}
     */
    public function show(Supplier $supplier, Layup $layup): JsonResponse
    {
        $this->authorize('view', $layup);
        $this->authorizeLayup($supplier, $layup);

        return response()->json([
            'success' => true,
            'data'    => new LayupResource($layup),
        ]);
    }

    /**
     * Update layup
     *
     * @bodyParam name string Nama layup. Example: Layup B
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Layup updated successfully.",
     *   "data": {"id": 1, "supplier_id": 1, "name": "Layup B", "created_at": "2024-01-01 00:00:00"}
     * }
     * @response 403 {"message": "This action is unauthorized."}
     * @response 404 {"message": "No query results for model [Layup]."}
     */
    public function update(UpdateLayupRequest $request, Supplier $supplier, Layup $layup): JsonResponse
    {
        $this->authorize('update', $layup);
        $this->authorizeLayup($supplier, $layup);

        $layup->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Layup updated successfully.',
            'data'    => new LayupResource($layup),
        ]);
    }

    /**
     * Hapus layup
     *
     * @response 200 {"success": true, "message": "Layup deleted successfully."}
     * @response 403 {"message": "This action is unauthorized."}
     * @response 404 {"message": "No query results for model [Layup]."}
     */
    public function destroy(Supplier $supplier, Layup $layup): JsonResponse
    {
        $this->authorize('delete', $layup);
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