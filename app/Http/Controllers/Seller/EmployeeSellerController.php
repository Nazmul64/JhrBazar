<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\EmployeeSeller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class EmployeeSellerController extends Controller
{
    public function index()
    {
        $employees = EmployeeSeller::where('seller_id', auth()->id())->latest()->get();
        return view('seller.employeeseller.index', compact('employees'));
    }

    public function create()
    {
        return view('seller.employeeseller.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'nullable|string|max:255',
            'phone'         => 'nullable|string|max:20',
            'gender'        => 'nullable|in:male,female,other',
            'email'         => 'nullable|email|max:255',
            'role'          => 'required|string|max:100',
            'password'      => 'required|string|min:6|confirmed',
            'address'       => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('profile_image')) {
            $imagePath = $this->uploadImage($request->file('profile_image'));
        }

        EmployeeSeller::create([
            'seller_id'     => auth()->id(),
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'phone'         => $request->phone,
            'gender'        => $request->gender ?? 'male',
            'email'         => $request->email,
            'role'          => $request->role,
            'password'      => Hash::make($request->password),
            'address'       => $request->address,
            'profile_image' => $imagePath,
        ]);

        return redirect()->route('seller.employeeseller.index')
            ->with('success', 'Employee created successfully.');
    }

    public function edit($id)
    {
        $employee = EmployeeSeller::where('seller_id', auth()->id())->findOrFail($id);
        return view('seller.employeeseller.edit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $employee = EmployeeSeller::where('seller_id', auth()->id())->findOrFail($id);

        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'nullable|string|max:255',
            'phone'         => 'nullable|string|max:20',
            'gender'        => 'nullable|in:male,female,other',
            'email'         => 'nullable|email|max:255',
            'role'          => 'required|string|max:100',
            'password'      => 'nullable|string|min:6|confirmed',
            'address'       => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'phone'      => $request->phone,
            'gender'     => $request->gender ?? 'male',
            'email'      => $request->email,
            'role'       => $request->role,
            'address'    => $request->address,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_image')) {
            $this->deleteImage($employee->profile_image);
            $data['profile_image'] = $this->uploadImage($request->file('profile_image'));
        }

        $employee->update($data);

        return redirect()->route('seller.employeeseller.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy($id)
    {
        $employee = EmployeeSeller::where('seller_id', auth()->id())->findOrFail($id);
        $this->deleteImage($employee->profile_image);
        $employee->delete();

        return redirect()->route('seller.employeeseller.index')
            ->with('success', 'Employee deleted successfully.');
    }

    private function uploadImage($file): string
    {
        $path = public_path('uploads/employeesellercrete');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($path, $fileName);
        return 'uploads/employeesellercrete/' . $fileName;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
