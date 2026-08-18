<?php
namespace App\RepositoryInterface;
interface ProductInterface
{
    public function getAllCategories();
    public function getUnits();
    public function getBrands();
    public function getAllProducts();
    public function getProductById($id);
    public function createProduct(array $data);
    public function updateProduct($id, array $data);
    public function deleteProduct($id);
    public function CountProducts();
}
