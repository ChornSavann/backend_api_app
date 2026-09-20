<?php

namespace App\RepositoryInterface;

interface ExpenseInterface
{
    public function getAll();
    public function getAllExpensetype();
    public function create(array $data);
    public function update(array $data, $id);
    public function findById($id);
    public function delete($id);
}
