<?php
namespace App\Services;
class ExpenseTypeService implements \App\Services\Interface\ExpenseTypeServiceInterface
{
    protected $expenseTypeRepository;

    public function __construct(\App\Repositories\ExpenseTypeRepository $expenseTypeRepository)
    {
        $this->expenseTypeRepository = $expenseTypeRepository;
    }

    public function getAll()
    {
        return $this->expenseTypeRepository->getAll();
    }

    public function create(array $data)
    {
        return $this->expenseTypeRepository->create($data);
    }

    public function update(array $data, $id)
    {
        return $this->expenseTypeRepository->update($data, $id);
    }

    public function findById($id)
    {
        return $this->expenseTypeRepository->findById($id);
    }

    public function delete($id)
    {
        return $this->expenseTypeRepository->delete($id);
    }
}