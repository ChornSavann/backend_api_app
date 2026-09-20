<?php
namespace App\Repositories;
use App\RepositoryInterface\BrandInterface;
use App\Models\Brand;
class BrandRepository implements BrandInterface
{
    protected $brand;

    public function __construct(Brand $brand)
    {
        $this->brand = $brand;
    }

    public function getAllBrands()
    {
        return $this->brand->withCount('products')->get();
    }

    public function getBrandById($id)
    {
        return $this->brand->findOrFail($id);
    }

    public function createBrand(array $data)
    {
        return $this->brand->create($data);
    }

    public function updateBrand($id, array $data)
    {
        $brand = $this->getBrandById($id);
        $brand->update($data);
        return $brand;
    }

    public function deleteBrand($id)
    {
        $brand = $this->getBrandById($id);
        return $brand->delete();
    }
}