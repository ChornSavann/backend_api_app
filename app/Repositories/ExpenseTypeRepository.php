<?php
namespace App\Repositories;
use App\RepositoryInterface\ExpenseTypeInterface;
class ExpenseTypeRepository implements ExpenseTypeInterface
{
    protected $expenseTypeModel;

    public function __construct(\App\Models\ExpenseType $expenseTypeModel)
    {
        $this->expenseTypeModel = $expenseTypeModel;
    }

    public function getAll()
    {
        return $this->expenseTypeModel->all();
    }

    public function create(array $data)
    {
        return $this->expenseTypeModel->create($data);
    }

    public function update(array $data, $id)
    {
        $expenseType = $this->findById($id);
        if (!$expenseType) {
            return null; 
        }
        return $expenseType->update($data);
    }

    public function findById($id)
    {
        return $this->expenseTypeModel->find($id);
    }

    public function delete($id)
    {
        $expenseType = $this->findById($id);
        if (!$expenseType) {
            return false; 
        }
        return $expenseType->delete();
    }
}