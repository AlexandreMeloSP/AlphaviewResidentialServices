<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exchange;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExchangeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $trocas = Exchange::where('user_proponente_id', $userId)
            ->orWhere('user_receptor_id', $userId)
            ->with(['serviceProponente', 'serviceReceptor', 'userProponente', 'userReceptor'])
            ->latest()
            ->paginate(15);

        setPaginationPrefix($trocas, '/api/exchanges');

        return response()->json($trocas);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->isApproved()) {
            return response()->json(['message' => 'Apenas usuários aprovados podem propor trocas.'], 403);
        }

        $request->validate([
            'service_proponente_id' => ['required', 'exists:services,id'],
            'service_receptor_id' => ['required', 'exists:services,id'],
        ], [
            'service_proponente_id.required' => 'O serviço do proponente é obrigatório.',
            'service_proponente_id.exists' => 'O serviço do proponente não foi encontrado.',
            'service_receptor_id.required' => 'O serviço do receptor é obrigatório.',
            'service_receptor_id.exists' => 'O serviço do receptor não foi encontrado.',
        ]);

        $servicoProponente = Service::findOrFail($request->service_proponente_id);
        $servicoReceptor = Service::findOrFail($request->service_receptor_id);

        if ($servicoProponente->user_id !== $user->id) {
            return response()->json(['message' => 'O serviço do proponente deve pertencer a você.'], 403);
        }

        if ($servicoProponente->user_id === $servicoReceptor->user_id) {
            return response()->json(['message' => 'Não é possível trocar serviços consigo mesmo.'], 422);
        }

        if ($servicoReceptor->status !== 'active') {
            return response()->json(['message' => 'O serviço do receptor não está disponível.'], 422);
        }

        $troca = new Exchange();
        $troca->forceFill([
            'service_proponente_id' => $servicoProponente->id,
            'service_receptor_id' => $servicoReceptor->id,
            'user_proponente_id' => $user->id,
            'user_receptor_id' => $servicoReceptor->user_id,
            'status' => 'pending',
        ])->save();

        return response()->json($troca->load([
            'serviceProponente',
            'serviceReceptor',
            'userProponente',
            'userReceptor',
        ]), 201);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $troca = Exchange::with([
            'serviceProponente',
            'serviceReceptor',
            'userProponente',
            'userReceptor',
        ])->findOrFail($id);

        $userId = $request->user()->id;
        if ($troca->user_proponente_id !== $userId && $troca->user_receptor_id !== $userId) {
            if (! $request->user()->isAdmin()) {
                return response()->json(['message' => 'Acesso negado.'], 403);
            }
        }

        return response()->json($troca);
    }

    public function confirm(Request $request, $id): JsonResponse
    {
        return DB::transaction(function () use ($request, $id) {
            $troca = Exchange::lockForUpdate()->findOrFail($id);
            $userId = $request->user()->id;

            if ($troca->user_receptor_id !== $userId) {
                return response()->json(['message' => 'Apenas o receptor pode confirmar esta troca.'], 403);
            }

            if ($troca->status !== 'pending') {
                return response()->json(['message' => 'Esta troca não pode ser confirmada.'], 422);
            }

            $troca->forceFill(['status' => 'confirmed'])->save();

            return response()->json($troca->load([
                'serviceProponente',
                'serviceReceptor',
                'userProponente',
                'userReceptor',
            ]));
        });
    }

    public function cancel(Request $request, $id): JsonResponse
    {
        $troca = Exchange::findOrFail($id);
        $userId = $request->user()->id;

        if ($troca->user_receptor_id !== $userId && $troca->user_proponente_id !== $userId) {
            return response()->json(['message' => 'Você não participa desta troca.'], 403);
        }

        if (in_array($troca->status, ['completed', 'cancelled'])) {
            return response()->json(['message' => 'Esta troca não pode ser cancelada.'], 422);
        }

        $troca->forceFill(['status' => 'cancelled'])->save();

        return response()->json($troca->load([
            'serviceProponente',
            'serviceReceptor',
            'userProponente',
            'userReceptor',
        ]));
    }
}
