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
    public function getBrandById($id);
    public function getCategoryById($id);
    public function getByBarcode($barcode);
     public function getProductsWithMinQty();
}
