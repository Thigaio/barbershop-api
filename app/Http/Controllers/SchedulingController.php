<?php

namespace App\Http\Controllers;

use App\Models\Scheduling;
use App\Http\Requests\UpdateSchedulingRequest;
use App\Models\User;
use App\Models\UserType;
use App\Notifications\NewSchedulingNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Schedulings",
 *     description="Gerenciamento de agendamentos"
 * )
 */

class SchedulingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * @OA\Get(
     *     path="/api/schedulings",
     *     summary="Lista todos os agendamentos (admins veem todos)",
     *     tags={"Schedulings"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Lista de agendamentos")
     * )
     */

   public function index()
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        if ($authUser->user_type_id === 1) {
            $schedulings = Scheduling::all();
        } else {
            $schedulings = Scheduling::where('user_id', $authUser->id)->get();
        }

        return response()->json($schedulings);
    }

    /**
     * @OA\Post(
     *     path="/api/schedulings",
     *     summary="Cria um agendamento",
     *     tags={"Schedulings"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"start_date","end_date"},
     *             @OA\Property(property="start_date", type="string", format="date-time", example="2025-10-25 14:00:00"),
     *             @OA\Property(property="end_date", type="string", format="date-time", example="2025-10-25 15:00:00")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Agendamento criado"),
     *     @OA\Response(response=404, description="Cliente não encontrado"),
     *     @OA\Response(response=422, description="Erro de validação")
     * )
     */

    public function store(Request $request)
    {
        $user = $request->user();
        $client = $user->client;
        
        if (!$client) {
            return response()->json(['message' => 'Cliente não encontrado para o usuário'], 404);
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);

        $scheduling = Scheduling::create([
            'client_id'  => $client->id,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
        ]);

        $adminUserType = UserType::where('name', 'admin')->first();
        $admins = User::where('user_type_id', $adminUserType->id)->get();

        foreach ($admins as $admin) {
            $admin->notify(new NewSchedulingNotification($scheduling));
        }

        return response()->json($scheduling, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/schedulings/{id}",
     *     summary="Exibe um agendamento do cliente logado",
     *     tags={"Schedulings"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Agendamento encontrado"),
     *     @OA\Response(response=404, description="Cliente ou agendamento não encontrado")
     * )
     */

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $client = $user->client;

        if (!$client) {
            return response()->json(['message' => 'Cliente não encontrado para o usuário'], 404);
        }

        $scheduling = Scheduling::where('client_id', $client->id)->findOrFail($id);

        return response()->json($scheduling);
    }

     /**
     * @OA\Put(
     *     path="/api/schedulings/{id}",
     *     summary="Atualiza um agendamento",
     *     tags={"Schedulings"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"start_date","end_date"},
     *             @OA\Property(property="start_date", type="string", format="date-time"),
     *             @OA\Property(property="end_date", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Agendamento atualizado"),
     *     @OA\Response(response=404, description="Agendamento não encontrado"),
     *     @OA\Response(response=422, description="Erro de validação")
     * )
     */

    public function update(UpdateSchedulingRequest $request, $id)
    {
        $scheduling = Scheduling::findOrFail($id);

        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);

        $scheduling->update([
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
        ]);

        return response()->json($scheduling);
    }

    /**
     * @OA\Delete(
     *     path="/api/schedulings/{id}",
     *     summary="Remove um agendamento",
     *     tags={"Schedulings"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Agendamento removido"),
     *     @OA\Response(response=404, description="Agendamento não encontrado")
     * )
     */

    public function destroy(Request $request, $id)
    {
        $scheduling = Scheduling::findOrFail($id);
        $scheduling->delete();

        return response()->json(['message' => 'Agendamento removido']);
    }

}
