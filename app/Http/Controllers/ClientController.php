<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Clients",
 *     description="Gerenciamento de clientes"
 * )
 */
class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['store']);
    }

    /**
     * @OA\Get(
     *     path="/api/clients",
     *     summary="Lista todos os clientes (admin only)",
     *     tags={"Clients"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Lista de clientes"),
     *     @OA\Response(response=403, description="Acesso negado")
     * )
     */

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

    /**
     * @OA\Get(
     *     path="/api/clients/{id}",
     *     summary="Exibe um cliente",
     *     tags={"Clients"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Cliente encontrado"),
     *     @OA\Response(response=403, description="Acesso negado"),
     *     @OA\Response(response=404, description="Cliente não encontrado")
     * )
     */

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

    /**
     * @OA\Put(
     *     path="/api/clients/{id}",
     *     summary="Atualiza um cliente",
     *     tags={"Clients"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Cliente atualizado"),
     *     @OA\Response(response=403, description="Acesso negado"),
     *     @OA\Response(response=404, description="Cliente não encontrado")
     * )
     */

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

     /**
     * @OA\Delete(
     *     path="/api/clients/{id}",
     *     summary="Remove um cliente",
     *     tags={"Clients"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Cliente removido"),
     *     @OA\Response(response=403, description="Acesso negado"),
     *     @OA\Response(response=404, description="Cliente não encontrado")
     * )
     */

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
