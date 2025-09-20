<?php

namespace App\Http\Controllers;

use App\Models\Scheduling;
use App\Http\Requests\UpdateSchedulingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchedulingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

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

        return response()->json($scheduling, 201);
    }

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

    public function update(UpdateSchedulingRequest $request, $id)
    {
        $user = $request->user();
        $client = $user->client;

        if (!$client) {
            return response()->json(['message' => 'Cliente não encontrado para o usuário'], 404);
        }

        $scheduling = Scheduling::where('client_id', $client->id)->findOrFail($id);

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

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $client = $user->client;

        if (!$client) {
            return response()->json(['message' => 'Cliente não encontrado para o usuário'], 404);
        }

        $scheduling = Scheduling::where('client_id', $client->id)->findOrFail($id);
        $scheduling->delete();

        return response()->json(['message' => 'Agendamento removido']);
    }
}
