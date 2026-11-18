<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'customer')->latest()->paginate(10);

        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.form', ['customer' => new User()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'password' => 'required|string|min:6',
        ]);
        $data['role'] = 'customer';
        User::create($data);

        return redirect()->route('admin.customers.index')->with('success', 'Customer berhasil ditambahkan.');
    }

    public function edit(User $customer)
    {
        abort_if($customer->role !== 'customer', 404);

        return view('admin.customers.form', compact('customer'));
    }

    public function update(Request $request, User $customer)
    {
        abort_if($customer->role !== 'customer', 404);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($customer->id)],
            'phone' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:6',
        ]);
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $customer->update($data);

        return redirect()->route('admin.customers.index')->with('success', 'Customer berhasil diperbarui.');
    }

    public function destroy(User $customer)
    {
        abort_if($customer->role !== 'customer', 404);
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer berhasil dihapus.');
    }
}
