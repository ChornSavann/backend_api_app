<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Interface\SupplierServiceInterface;
use Exception;

class SupplierController extends Controller
{
    protected $supplierService;

    public function __construct(SupplierServiceInterface $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    
    public function index()
    {
        try {
            $suppliers = $this->supplierService->getAllSuppliers();

            return response()->json([
                'success' => true,
                'data'    => $suppliers
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve suppliers.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // ២. បង្កើត Supplier ថ្មី
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $supplier = $this->supplierService->createSupplier($data);

            return response()->json([
                'success' => true,
                'message' => 'Supplier created successfully!',
                'data'    => $supplier
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create supplier.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // ៣. មើលព័ត៌មាន Supplier តាម ID
    public function show($id)
    {
        try {
            $supplier = $this->supplierService->getSupplierById($id);

            if (!$supplier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Supplier not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data'    => $supplier
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve supplier.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // ៤. កែប្រែព័ត៌មាន Supplier
    public function update(Request $request, $id)
    {
        try {
            $data = $request->all();
            $supplier = $this->supplierService->updateSupplier($id, $data);

            if (!$supplier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Supplier not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Supplier updated successfully!',
                'data'    => $supplier
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update supplier.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // ៥. លុប Supplier
    public function destroy($id)
    {
        try {
            $deleted = $this->supplierService->deleteSupplier($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Supplier not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Supplier deleted successfully!'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete supplier.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}