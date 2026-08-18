<?php
namespace App\Services\Interface;
interface CustomerServiceInterface
{
    public function getAllCustomers();
    public function getCustomerById($id);
    public function createCustomer(array $data);
    public function updateCustomer($id, array $data);
    public function deleteCustomer($id);
}