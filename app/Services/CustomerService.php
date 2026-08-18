<?php
namespace App\Services;
class CustomerService implements \App\Services\Interface\CustomerServiceInterface
{
    protected $customerRepository;

    public function __construct(\App\RepositoryInterface\CustomerInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function getAllCustomers()
    {
        return $this->customerRepository->getAllCustomers();
    }

    public function getCustomerById($id)
    {
        return $this->customerRepository->getCustomerById($id);
    }

    public function createCustomer(array $data)
    {
        return $this->customerRepository->createCustomer($data);
    }

    public function updateCustomer($id, array $data)
    {
        return $this->customerRepository->updateCustomer($id, $data);
    }

    public function deleteCustomer($id)
    {
        return $this->customerRepository->deleteCustomer($id);
    }
}