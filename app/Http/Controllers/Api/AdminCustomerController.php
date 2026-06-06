<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\User; // Assuming User model represents customers
use Illuminate\Http\Request;

class AdminCustomerController extends Controller
{
    /**
     * List all customers (admin view).
     */
    public function listAll()
    {
        $customers = User::where('role', 'customer')->get();
        return CustomerResource::collection($customers);
    }

    /**
     * Show a specific customer details for admin.
     */
    public function show($id)
    {
        $customer = User::where('role', 'customer')->findOrFail($id);
        return new CustomerResource($customer);
    }
}
