<?php

namespace App\RepositoryInterface;
interface BrandInterface
{
    public function getAllBrands();
    public function getBrandById($id);
    public function createBrand(array $data);
    public function updateBrand($id, array $data);
    public function deleteBrand($id);
}