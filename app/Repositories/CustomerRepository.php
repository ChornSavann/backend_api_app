<?php
namespace App\Repositories;
class CustomerRepository implements \App\RepositoryInterface\CustomerInterface
{
    public function getAllCustomers()
    {
        return \App\Models\Customer::all();
    }

    public function getCustomerById($id)
    {
        return \App\Models\Customer::findOrFail($id);
    }

    public function createCustomer(array $data)
    {
        return \App\Models\Customer::create($data);
    }

    public function updateCustomer($id, array $data)
    {
        $customer = \App\Models\Customer::findOrFail($id);
        $customer->update($data);
        return $customer;
    }

    public function deleteCustomer($id)
    {
        $customer = \App\Models\Customer::findOrFail($id);
        $customer->delete();
        return true;
    }
}