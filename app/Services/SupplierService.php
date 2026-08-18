<?php
namespace App\Services;
class SupplierService implements \App\Services\Interface\SupplierServiceInterface
{
    protected $supplierRepository;

    public function __construct(\App\RepositoryInterface\SupplierInterface $supplierRepository)
    {
        $this->supplierRepository = $supplierRepository;
    }

    public function createSupplier(array $data)
    {
        return $this->supplierRepository->createSupplier($data);
    }

    public function getSupplierById(int $supplierId)
    {
        return $this->supplierRepository->getSupplierById($supplierId);
    }

    public function getAllSuppliers()
    {
        return $this->supplierRepository->getAllSuppliers();
    }

    public function updateSupplier(int $supplierId, array $data)
    {
        return $this->supplierRepository->updateSupplier($supplierId, $data);
    }

    public function deleteSupplier(int $supplierId)
    {
        return $this->supplierRepository->deleteSupplier($supplierId);
    }
}