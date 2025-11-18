<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('super.admin');
    }

    public function index()
    {
        $businesses = Business::with('owner')->paginate(10);
        return view('superadmin.index', compact('businesses'));
    }

    public function create()
    {
        return view('superadmin.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'owner_email' => 'required|email|max:255|unique:users,email',
            'type' => 'required|in:clinica,salao,consultorio,outro',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $owner = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['owner_email'],
            'password' => bcrypt($validated['password']),
        ]);

        $business = Business::create([
            'owner_id' => $owner->id,
            'name' => $validated['name'],
            'type' => $validated['type'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
        ]);

        $business->settings()->create(); // Create default settings

        $owner->update(['business_id' => $business->id]);

        return redirect()->route('superadmin.index')->with('status', 'Estabelecimento criado com sucesso!');
    }

    public function edit(Business $business)
    {
        return view('superadmin.edit', compact('business'));
    }

    public function update(Request $request, Business $business)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:clinica,salao,consultorio,outro',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'active' => 'required|boolean',
        ]);

        $business->update($validated);

        return redirect()->route('superadmin.index')->with('status', 'Estabelecimento atualizado com sucesso!');
    }

    public function destroy(Business $business)
    {
        $business->delete();
        return redirect()->route('superadmin.index')->with('status', 'Estabelecimento excluído com sucesso!');
    }
}
