<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Services\Interface\BrandServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
class BrandController extends Controller
{
    protected $brandService;
    public function __construct(BrandServiceInterface $brandService)
    {
        $this->brandService = $brandService;
    }

    public function index():JsonResponse    
    {
        $brands = $this->brandService->getAllBrands();
        return response()->json([
            'success' => true,
            'data' => $brands
        ], 200);
    }

    
    public function create():JsonResponse
    {
       $this->brandService->createBrand($data);
        return response()->json([
            'success' => true,
            'data' => $brand
        ], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string', 
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' 
        ]);


        $validatedData['slug'] = \Illuminate\Support\Str::slug($request->name);
        if ($request->hasFile('logo')) {
            $validatedData['logo'] = $request->file('logo');
        }

        $brand = $this->brandService->createBrand($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Brand created successfully',
            'data' => $brand
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        return response()->json([
            'success' => true,
            'data' => $brand
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        $this->brandService->getBrandById($brand->id);
        return response()->json([
            'success' => true,
            'data' => $brand
        ], 200);
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' 
        ]);

        $validatedData['slug'] = \Illuminate\Support\Str::slug($request->name);

        if ($request->hasFile('logo')) {
            $validatedData['logo'] = $request->file('logo');
        }

        $brand = $this->brandService->updateBrand($id, $validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Brand updated successfully with logo!',
            'data' => $brand
        ], 200);
    }

   
    public function destroy($id)
    {
        try {
            
            $this->brandService->deleteBrand($id);

            return response()->json([
                'success' => true,
                'message' => 'Brand and its logo deleted successfully!'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Brand not found or could not be deleted.',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
