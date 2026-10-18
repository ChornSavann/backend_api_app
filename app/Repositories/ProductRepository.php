<?php

namespace App\Repositories;

use App\Models\Product;
use App\RepositoryInterface\ProductInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ProductRepository implements ProductInterface
{
    public function getAllProducts()
    {
        return Product::with(['category', 'brand', 'unit'])->latest()->get();
    }

    public function getProductsWithMinQty()
    {

        return Product::with(['category', 'brand', 'unit'])
            ->where('stock_quantity', '>=', 10)
            ->latest()
            ->get();
    }

    public function CountProducts()
    {
        return Product::count();
    }

    public function getAllCategories()
    {
        return Product::with('category')->get()->pluck('category')->unique();
    }

    public function getUnits()
    {

        return Product::with('unit')->get()->pluck('unit')->unique();
    }

    public function getBrands()
    {

        return Product::with('brand')->get()->pluck('brand')->unique();
    }


    public function getProductById($id)
    {
        return Product::with(['category', 'brand', 'unit'])->findOrFail($id);
    }

    private function uploadImage($file, $name)
    {
        $imageName = time() . '_' . Str::slug($name) . '.' . $file->extension();

        Log::info("Trying to save image to: " . public_path('products') . '/' . $imageName);

        $file->move(public_path('products'), $imageName);

        return 'products/' . $imageName;
    }
    private function generateSlug(?string $name, ?string $sku, ?int $excludeId = null): string
    {
        $slug = Str::slug($name ?? '');
        if (empty($slug)) {
            $slug = Str::slug($sku ?? '') ?: 'product-' . time();
        }

        $originalSlug = $slug;
        $count = 1;
        $query = Product::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;

            $query = Product::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }
    public function createProduct(array $data)
    {
        $data['slug'] = $this->generateSlug($data['name'] ?? null, $data['sku'] ?? null);
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $data['image'] = $this->uploadImage($data['image'], $data['name']);
        }
        return Product::create($data);
    }


    public function updateProduct($id, array $data)
    {
        $product = Product::findOrFail($id);
        // 🔹 Generate Slug ថ្មីបើមានការប្ដូរឈ្មោះ
        if (isset($data['name'])) {
            $data['slug'] = $this->generateSlug($data['name'], $data['sku'] ?? $product->sku, $id);
        }
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            if ($product->image) {
                $oldImagePath = public_path($product->image);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }
            $data['image'] = $this->uploadImage($data['image'], $data['name'] ?? $product->name);
        }

        $product->update($data);

        return $product;
    }
    public function deleteProduct($id)
    {
        $product = Product::find($id);

        if ($product) {

            if ($product->image) {
                $imagePath = public_path($product->image);
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
            }
            return $product->delete();
        }
        return false;
    }

    public function getBrandById($id)
    {
        return Product::where('brand_id', $id)->get();
    }

    public function getCategoryById($id)
    {
        return Product::where('category_id', $id)->get();
    }

    public function getByBarcode($barcode)
    {
        return Product::where('barcode', $barcode)->first();
    }
}
