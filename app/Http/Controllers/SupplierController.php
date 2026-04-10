<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSuppliersRequest;
use App\Http\Requests\UpdateSuppliersRequest;
use App\Http\Resources\SupplierResources;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
    /**
     * List semua supplier
     *
     * Mengambil daftar semua supplier dengan pagination.
     *
     * @queryParam page int Nomor halaman. Example: 1
     * @queryParam per_page int Jumlah data per halaman. Example: 10
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "PT Graha Kalasta"
     *      "created_at": "2024-01-01 00:00:00",
     *      "updated_at": "2024-01-01 00:00:00"
     *     }
     *   ],
     *   "meta": {
     *     "total": 1,
     *     "per_page": 10,
     *     "current_page": 1,
     *     "last_page": 1
     *   }
     * }
     */
    public function index(): JsonResponse
    {
        $suppliers = Supplier::oldest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => SupplierResources::collection($suppliers),
            'meta' => [
                'current_page' => $suppliers->currentPage(),
                'last_page' => $suppliers->lastPage(),
                'per_page' => $suppliers->perPage(),
                'total' => $suppliers->total(),
            ],
        ]);
    }

    /**
     * Tambah supplier baru
     *
     * Membuat data supplier baru ke database.
     *
     * @bodyParam name string required Nama supplier. Example: PT Graha Kalasta
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Supplier created successfully.",
     *   "data": {
     *     "id": 1,
     *     "name": "PT Graha Kalasta",
     *     "created_at": "2024-01-01 00:00:00",
     *    "updated_at": "2024-01-01 00:00:00"
     *   }
     * }
     */
    public function store(StoreSuppliersRequest $request): JsonResponse
    {
        $supplier = Supplier::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Supplier created successfully',
            'data' => new SupplierResources($supplier),
        ], 201);
    }

    /**
     * Detail supplier
     *
     * Mengambil detail satu supplier berdasarkan ID.
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "PT Maju Jaya",
     *     "created_at": "2024-01-01 00:00:00",
     *     "updated_at": "2024-01-01 00:00:00"
     *   }
     *   }
     * }
     * @response 404 {
     *   "message": "No query results for model [Supplier]."
     * }
     */
    public function show(Supplier $supplier): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new SupplierResources($supplier),
        ]);
    }

    /**
     * Update supplier
     *
     * Mengubah data supplier yang sudah ada.
     *
     * @urlParam supplier int required ID supplier. Example: 1
     *
     * @bodyParam name string Nama supplier. Example: PT Sejahtera
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Supplier updated successfully.",
     *   "data": {
     *     "id": 1,
     *     "name": "PT Graha Kalasta",
     *     "created_at": "2024-01-01 00:00:00",
     *     "updated_at": "2024-01-01 00:00:00"
     *   }
     * }
     */
    public function update(UpdateSuppliersRequest $request, Supplier $supplier): JsonResponse
    {
        $supplier->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Update Supplier Successfully',
            'data' => new SupplierResources($supplier),
        ]);
    }

    /**
     * Hapus supplier
     *
     * Menghapus data supplier dari database.
     *
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Supplier deleted successfully."
     * }
     * @response 404 {
     *   "message": "No query results for model [Supplier]."
     * }
     */
    public function destroy(Supplier $supplier): JsonResponse
    {
        $supplier->delete();

        return response()->json([
            'success' => true,
            'message' => 'Supplier deleted successfully',
        ]);
    }
}
