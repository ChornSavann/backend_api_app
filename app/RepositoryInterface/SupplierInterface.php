<?php
namespace App\RepositoryInterface;
interface SupplierInterface
{
    public function createSupplier(array $data);
    public function getSupplierById(int $supplierId);
    public function getAllSuppliers();
    public function updateSupplier(int $supplierId, array $data);
    public function deleteSupplier(int $supplierId);
}