<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $this->authorize('admin-only');
        return User::where('user_type_id', 1)->get();
    }

    public function store(Request $request)
    {
        $this->authorize('admin-only');

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $admin = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'user_type_id' => 1, // admin
        ]);

        return response()->json($admin, 201);
    }

    public function show($id)
    {
        $this->authorize('admin-only');
        return User::where('user_type_id', 1)->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin-only');

        $admin = User::where('user_type_id', 1)->findOrFail($id);

        $admin->update($request->only(['name', 'email']));

        return response()->json($admin);
    }

    public function destroy($id)
    {
        $this->authorize('admin-only');

        $admin = User::where('user_type_id', 1)->findOrFail($id);
        $admin->delete();

        return response()->json(['message' => 'Administrador removido']);
    }
}
