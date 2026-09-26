<?php
namespace App\RepositoryInterface;
interface CustomerInterface
{
    public function getAllCustomers();
    public function getCustomerById($id);
    public function createCustomer(array $data);
    public function updateCustomer($id, array $data);
    public function deleteCustomer($id);
    public function getCustomerByPhone($phone);
     public function getOrdersByCustomerId($id);
}