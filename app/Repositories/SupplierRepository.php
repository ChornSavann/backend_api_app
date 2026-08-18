<?php
namespace App\Repositories;
class SupplierRepository implements \App\RepositoryInterface\SupplierInterface
{
    public function createSupplier(array $data)
    {
        return \App\Models\Supplier::create($data);
    }

    public function getSupplierById(int $supplierId)
    {
        return \App\Models\Supplier::find($supplierId);
    }

    public function getAllSuppliers()
    {
        return \App\Models\Supplier::all();
    }

    public function updateSupplier(int $supplierId, array $data)
    {
        $supplier = \App\Models\Supplier::find($supplierId);
        if ($supplier) {
            $supplier->update($data);
            return $supplier;
        }
        return null;
    }

    public function deleteSupplier(int $supplierId)
    {
        $supplier = \App\Models\Supplier::find($supplierId);
        if ($supplier) {
            $supplier->delete();
            return true;
        }
        return false; 
    }
    
    
}