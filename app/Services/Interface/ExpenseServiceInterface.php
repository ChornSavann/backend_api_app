<?php
namespace App\Services\Interface;
interface ExpenseServiceInterface
{
    public function getAll();
    public function getALlexpensetype();
    public function create(array $data);
    public function update(array $data, $id);
    public function findById($id);
    public function delete($id);
}