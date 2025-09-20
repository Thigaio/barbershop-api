<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['store']);
    }

    public function index()
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        if ($authUser->user_type_id !== 1) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $clients = User::where('user_type_id', 2)->get();

        return response()->json($clients);
}


    public function show($id)
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        $user = User::where('user_type_id', 2)->findOrFail($id);

        if ($authUser->user_type_id === 1) {
            return $user;
        }

        if ($authUser->id === $user->id) {
            return $user;
        }

        return response()->json(['message' => 'Acesso negado'], 403);
    }

    public function update(Request $request, $id)
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        $user = User::where('user_type_id', 2)->findOrFail($id);

        if ($authUser->user_type_id !== 1 && $authUser->id !== $user->id) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $user->update($request->only(['name', 'email']));

        return response()->json($user);
    }

    public function destroy($id)
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        $user = User::where('user_type_id', 2)->findOrFail($id);

        if ($authUser->user_type_id !== 1 && $authUser->id !== $user->id) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'Cliente removido']);
    }
}
