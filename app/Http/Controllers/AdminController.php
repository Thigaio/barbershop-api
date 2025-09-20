<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Tag(
 *     name="Admins",
 *     description="Gerenciamento de administradores"
 * )
 */
class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * @OA\Get(
     *     path="/api/admins",
     *     summary="Lista todos os administradores",
     *     tags={"Admins"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Lista de admins"),
     *     @OA\Response(response=403, description="Acesso negado")
     * )
     */

    public function index()
    {
        $this->authorize('admin-only');
        return User::where('user_type_id', 1)->get();
    }

    /**
     * @OA\Post(
     *     path="/api/admins",
     *     summary="Cria um novo administrador",
     *     tags={"Admins"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="Admin"),
     *             @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="secret"),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Administrador criado"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=403, description="Acesso negado")
     * )
     */

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

    /**
     * @OA\Get(
     *     path="/api/admins/{id}",
     *     summary="Exibe um administrador",
     *     tags={"Admins"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do administrador",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Administrador encontrado"),
     *     @OA\Response(response=404, description="Administrador não encontrado"),
     *     @OA\Response(response=403, description="Acesso negado")
     * )
     */

    public function show($id)
    {
        $this->authorize('admin-only');
        return User::where('user_type_id', 1)->findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/admins/{id}",
     *     summary="Atualiza um administrador",
     *     tags={"Admins"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Administrador atualizado"),
     *     @OA\Response(response=404, description="Administrador não encontrado"),
     *     @OA\Response(response=403, description="Acesso negado")
     * )
     */

    public function update(Request $request, $id)
    {
        $this->authorize('admin-only');

        $admin = User::where('user_type_id', 1)->findOrFail($id);

        $admin->update($request->only(['name', 'email']));

        return response()->json($admin);
    }

    /**
     * @OA\Delete(
     *     path="/api/admins/{id}",
     *     summary="Remove um administrador",
     *     tags={"Admins"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Administrador removido"),
     *     @OA\Response(response=404, description="Administrador não encontrado"),
     *     @OA\Response(response=403, description="Acesso negado")
     * )
     */

    public function destroy($id)
    {
        $this->authorize('admin-only');

        $admin = User::where('user_type_id', 1)->findOrFail($id);
        $admin->delete();

        return response()->json(['message' => 'Administrador removido']);
    }
}
