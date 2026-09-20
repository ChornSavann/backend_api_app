<?php
namespace App\RepositoryInterface;
interface ExpenseTypeInterface
{
    public function getAll();
    public function create(array $data);
    public function update(array $data, $id);
    public function findById($id);
    public function delete($id);
}