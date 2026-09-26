<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Services\Interface\CustomerServiceInterface;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    protected $customerService;
    public function __construct(CustomerServiceInterface $customerService)
    {
        $this->customerService = $customerService;
    }
    public function index(): JsonResponse
    {
        $customers = $this->customerService->getAllCustomers();
        return response()->json([
            'success' => true,
            'data' => $customers
        ], 200);
    }

    public function getCustomerByPhone(string $phone): JsonResponse
    {
        $customer = $this->customerService->getCustomerByPhone($phone);

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $customer
        ], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'points' => 'nullable|integer|min:0',
            'address' => 'nullable|string|max:500',
        ]);

        $customer = $this->customerService->createCustomer($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully.',
            'data' => $customer
        ], 201);
    }


    public function show($id)
    {
        $customer = $this->customerService->getCustomerById($id);

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $customer
        ], 200);
    }


    public function edit(Customer $customer, $id)
    {
        $customer = $this->customerService->getCustomerById($id);
        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        } else {
            return response()->json([
                'success' => true,
                'data' => $customer
            ], 200);
        }
    }



    public function update(Request $request, $id)
    {

        $customer = $this->customerService->getCustomerById($id);

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer not found'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:customers,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'points' => 'nullable|integer|min:0',
            'address' => 'nullable|string|max:500',
        ]);

        $updatedCustomer = $this->customerService->updateCustomer($id, $validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully.',
            'data' => $updatedCustomer
        ], 200);
    }

    public function destroy($id)
    {
        $this->customerService->deleteCustomer($id);

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully.'
        ], 200);
    }

    public function search(Request $request)
    {
        $name = $request->query('name');
        $customers = Customer::where('name', 'LIKE', "%{$name}%")->get();

        return response()->json([
            'success' => true,
            'data' => $customers
        ]);
    }

    public function getCustomerOrders($id): JsonResponse
    {
        $data = $this->customerService->getCustomerWithOrders($id);

        if (!$data) {
            return response()->json([
                'success' => false, 
                'message' => 'Customer not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'customer' => $data['customer'],
            'orders' => $data['orders']
        ], 200);
    }
}
