<?php

namespace App\Services;

use App\RepositoryInterface\ProductInterface;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getCountProducts()
    {
        return $this->productRepository->CountProducts();
    }

    public function getAllProducts()
    {
        return $this->productRepository->getAllProducts();
    }

    public function getAllCategories()
    {
        return $this->productRepository->getAllCategories();
    }

    public function getUnits()
    {
        return $this->productRepository->getUnits();
    }

    public function getBrands()
    {
        return $this->productRepository->getBrands();
    }

    public function getProductById($id)
    {
        return $this->productRepository->getProductById($id);
    }

    public function createProduct(array $data)
    {
        return $this->productRepository->createProduct($data);
    }

    public function updateProduct($id, array $data)
    {
        return $this->productRepository->updateProduct($id, $data);
    }

    public function deleteProduct($id)
    {
        return $this->productRepository->deleteProduct($id);
    }
    public function getBrandById($id)
    {
        return $this->productRepository->getBrandById($id);
    }

    public function getCategoryById($id)
    {
        return $this->productRepository->getCategoryById($id);
    }
}