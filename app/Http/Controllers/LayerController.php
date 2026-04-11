<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLayerRequest;
use App\Http\Requests\UpdateLayerRequest;
use App\Http\Resources\LayerResource;
use App\Models\Layup;
use App\Models\Layers;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;

/**
 * @group Layer Management
 *
 * API endpoints untuk mengelola data layer milik layup.
 */
class LayerController extends Controller
{
    /**
     * List semua layer milik layup
     *
     * @response 200 {
     *   "success": true,
     *   "data": [{"id": 1, "layup_id": 1, "layer_order": 1, "thickness": 2.5, "width": 100, "angle": 45, "created_at": "2024-01-01 00:00:00"}],
     *   "meta": {"total": 1, "per_page": 10, "current_page": 1, "last_page": 1}
     * }
     */
    public function index(Supplier $supplier, Layup $layup): JsonResponse
    {
        $this->authorize('viewAny', Layers::class);

        if (auth()->user()->isSupplier()) {
            $user = auth()->user()->load('supplier');
            abort_if(!$user->supplier, 403, 'User tidak terhubung ke supplier manapun.');
            abort_if($user->supplier->id !== $supplier->id, 403, 'Akses ditolak.');
        }

        $layers = $layup->layers()->orderBy('layer_order')->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => LayerResource::collection($layers),
            'meta'    => [
                'total'        => $layers->total(),
                'per_page'     => $layers->perPage(),
                'current_page' => $layers->currentPage(),
                'last_page'    => $layers->lastPage(),
            ],
        ]);
    }

    /**
     * Tambah layer baru ke layup
     *
     * @bodyParam layer_order integer required Urutan layer. Example: 1
     * @bodyParam thickness number required Ketebalan layer. Example: 2.5
     * @bodyParam width number required Lebar layer. Example: 100
     * @bodyParam angle number required Sudut layer (-180 hingga 180). Example: 45
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Layer created successfully.",
     *   "data": {"id": 1, "layup_id": 1, "layer_order": 1, "thickness": 2.5, "width": 100, "angle": 45}
     * }
     * @response 403 {"message": "This action is unauthorized."}
     */
    public function store(StoreLayerRequest $request, Supplier $supplier, Layup $layup): JsonResponse
    {
        $this->authorize('create', Layers::class);

        if (auth()->user()->isSupplier()) {
            $user = auth()->user()->load('supplier');
            abort_if(!$user->supplier, 403, 'User tidak terhubung ke supplier manapun.');
            abort_if($user->supplier->id !== $supplier->id, 403, 'Akses ditolak.');
        }

        $layer = $layup->layers()->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Layer created successfully.',
            'data'    => new LayerResource($layer),
        ], 201);
    }

    /**
     * Detail layer
     *
     * @response 200 {
     *   "success": true,
     *   "data": {"id": 1, "layup_id": 1, "layer_order": 1, "thickness": 2.5, "width": 100, "angle": 45}
     * }
     * @response 403 {"message": "This action is unauthorized."}
     * @response 404 {"message": "No query results for model [Layers]."}
     */
    public function show(Supplier $supplier, Layup $layup, Layers $layer): JsonResponse
    {
        $this->authorize('view', $layer);
        $this->authorizeLayer($layup, $layer);

        return response()->json([
            'success' => true,
            'data'    => new LayerResource($layer),
        ]);
    }

    /**
     * Update layer
     *
     * @bodyParam layer_order integer Urutan layer. Example: 2
     * @bodyParam thickness number Ketebalan layer. Example: 3.0
     * @bodyParam width number Lebar layer. Example: 120
     * @bodyParam angle number Sudut layer. Example: 90
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Layer updated successfully.",
     *   "data": {"id": 1, "layup_id": 1, "layer_order": 2, "thickness": 3.0, "width": 120, "angle": 90}
     * }
     * @response 403 {"message": "This action is unauthorized."}
     * @response 404 {"message": "No query results for model [Layers]."}
     */
    public function update(UpdateLayerRequest $request, Supplier $supplier, Layup $layup, Layers $layer): JsonResponse
    {
        $this->authorize('update', $layer);
        $this->authorizeLayer($layup, $layer);

        $layer->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Layer updated successfully.',
            'data'    => new LayerResource($layer),
        ]);
    }

    /**
     * Hapus layer
     *
     * @response 200 {"success": true, "message": "Layer deleted successfully."}
     * @response 403 {"message": "This action is unauthorized."}
     * @response 404 {"message": "No query results for model [Layers]."}
     */
    public function destroy(Supplier $supplier, Layup $layup, Layers $layer): JsonResponse
    {
        $this->authorize('delete', $layer);
        $this->authorizeLayer($layup, $layer);

        $layer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Layer deleted successfully.',
        ]);
    }

    /**
     * Pastikan layer memang milik layup yang dimaksud
     */
    private function authorizeLayer(Layup $layup, Layers $layer): void
    {
        abort_if(
            $layer->layup_id !== $layup->id,
            403,
            'Layer does not belong to this layup.'
        );
    }
}