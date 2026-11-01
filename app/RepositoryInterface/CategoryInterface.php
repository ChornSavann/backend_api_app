<?php
namespace App\RepositoryInterface;
interface CategoryInterface
{
    public function getAllCategories();
    public function getCategoryById(int $id);
    public function createCategory(array $data);
    public function updateCategory(int $id, array $data);
    public function deleteCategory(int $id);
}
