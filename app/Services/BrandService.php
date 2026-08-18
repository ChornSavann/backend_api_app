<?php
namespace App\Services;
use App\Services\Interface\BrandServiceInterface;
use App\RepositoryInterface\BrandInterface; // 👈 ហៅ Interface មកប្រើជំនួស

class BrandService implements BrandServiceInterface
{
    protected $brandRepository;

    // ទទួលយក BrandInterface តាមរយៈ Constructor Binding
    public function __construct(BrandInterface $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    public function getAllBrands()
    {
        return $this->brandRepository->getAllBrands();
    }

    public function getBrandById($id)
    {
        return $this->brandRepository->getBrandById($id);
    }

    
    public function createBrand(array $data)
    {
    
        if (isset($data['logo']) && $data['logo']->isValid()) {
            $file = $data['logo'];
            
            $filename = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
            
            $file->move(public_path('brands'), $filename);
            
            $data['logo'] = 'brands/' . $filename;
        }

        return $this->brandRepository->createBrand($data);
    }

    public function updateBrand($id, array $data)
    {
        $brand = $this->brandRepository->getBrandById($id);
        if (isset($data['logo']) && $data['logo']->isValid()) {
            
            if ($brand->logo && file_exists(public_path($brand->logo))) {
                unlink(public_path($brand->logo));
            }
            $file = $data['logo'];
            $filename = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('brands'), $filename);
            
            // កំណត់ Path ថ្មី
            $data['logo'] = 'brands/' . $filename;
        } else {
            // ប្រសិនបើមិនបានប្តូររូបភាពថ្មីទេ គឺរក្សារូបភាពដដែល
            unset($data['logo']);
        }

        return $this->brandRepository->updateBrand($id, $data);
    }

    public function deleteBrand($id)
    {
        $brand = $this->brandRepository->getBrandById($id);
        if ($brand->logo && file_exists(public_path($brand->logo))) {
            unlink(public_path($brand->logo));
        }
        return $this->brandRepository->deleteBrand($id);
    }
}