<?php

namespace App\Repositories;

use App\RepositoryInterface\ExpenseInterface;

class ExpenseRepository implements ExpenseInterface
{
    protected $expense;

    public function __construct(\App\Models\Expense $expense)
    {
        $this->expense = $expense;
    }

    public function getAll()
    {
        return $this->expense->with('expenseType', 'user')->get()->all();
    }

    public function getAllExpensetype()
    {
        return $this->expense->with('expenseType')->get();
    }

    public function create(array $data)
    {
        return $this->expense->create($data);
    }

    public function update(array $data, $id)
    {
        $expense = $this->findById($id);
        if ($expense) {
            $expense->update($data);
            return $expense;
        }
        return null;
    }

    public function findById($id)
    {
        return $this->expense->with('expenseType', 'user')->find($id);
    }

    public function delete($id)
    {
        $expense = $this->findById($id);
        if ($expense) {
            return $expense->delete();
        }
        return false;
    }
}
