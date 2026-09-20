<?php
namespace App\Services;

use App\Services\Interface\ExpenseServiceInterface;
use App\Repositories\ExpenseRepository;

class ExpenseService implements ExpenseServiceInterface
{
    protected $expenseRepository;

    public function __construct(ExpenseRepository $expenseRepository)
    {
        $this->expenseRepository = $expenseRepository;
    }

    public function getAll()
    {
        return $this->expenseRepository->getAll();
    }

    public function getAllexpensetype()
    {
        return $this->expenseRepository->getAllExpensetype();
    }

    public function create(array $data)
    {
        return $this->expenseRepository->create($data);
    }

    public function update(array $data, $id)
    {
        
        $expense = $this->expenseRepository->findById($id);
        
        if (!$expense) {
            return null; 
        }

        return $this->expenseRepository->update($data, $id);
    }

    public function delete($id)
    {
        return $this->expenseRepository->delete($id);
    }

    public function findById($id)
    {  
        return $this->expenseRepository->findById($id);
    }
}