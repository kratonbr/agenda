<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::where('business_id', Auth::user()->business_id)->paginate(10);
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $business = Auth::user()->business;
        $settings = $business->settings;

        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone,NULL,id,business_id,' . $business->id,
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
        ];

        if ($settings->requires_cpf) {
            $rules['cpf'] = 'required|string|size:11|unique:customers,cpf,NULL,id,business_id,' . $business->id;
        } else {
            $rules['cpf'] = 'nullable|string|size:11|unique:customers,cpf,NULL,id,business_id,' . $business->id;
        }

        $validated = $request->validate($rules);
        $validated['business_id'] = $business->id;
        $validated['created_by'] = Auth::id();

        Customer::create($validated);

        return redirect()->route('customers.index')->with('status', 'Cliente cadastrado com sucesso!');
    }

    public function edit(Customer $customer)
    {
        if ($customer->business_id !== Auth::user()->business_id) {
            abort(403);
        }
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        if ($customer->business_id !== Auth::user()->business_id) {
            abort(403);
        }

        $business = Auth::user()->business;
        $settings = $business->settings;

        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone,' . $customer->id . ',id,business_id,' . $business->id,
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
        ];

        if ($settings->requires_cpf) {
            $rules['cpf'] = 'required|string|size:11|unique:customers,cpf,' . $customer->id . ',id,business_id,' . $business->id;
        } else {
            $rules['cpf'] = 'nullable|string|size:11|unique:customers,cpf,' . $customer->id . ',id,business_id,' . $business->id;
        }

        $validated = $request->validate($rules);

        $customer->update($validated);

        return redirect()->route('customers.index')->with('status', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->business_id !== Auth::user()->business_id) {
            abort(403);
        }
        $customer->delete();
        return redirect()->route('customers.index')->with('status', 'Cliente excluído com sucesso!');
    }
}
