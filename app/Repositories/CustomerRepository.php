<?php
namespace App\Repositories;
use App\Models\Customer;
use App\Models\Order;
class CustomerRepository implements \App\RepositoryInterface\CustomerInterface
{
    public function getAllCustomers()
    {
        return $customers = Customer::withCount('orders')->latest()->get();
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

    public function getCustomerByPhone($phone)
    {
        return \App\Models\Customer::where('phone', $phone)->get();
    }

    public function getOrdersByCustomerId($id)
    {
        return Order::where('customer_id', $id)
                    ->with('details.product')
                    ->latest()
                    ->get();
    }
}