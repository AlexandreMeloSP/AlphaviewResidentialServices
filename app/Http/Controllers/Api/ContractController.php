<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    private function authorizeContractAccess(Contract $contrato, $userId, bool $allowAdmin = true): ?JsonResponse
    {
        if ($contrato->user_1_id !== $userId && $contrato->user_2_id !== $userId) {
            if ($allowAdmin && request()->user()->isAdmin()) {
                return null;
            }
            return response()->json(['message' => 'Acesso negado.'], 403);
        }
        return null;
    }

    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $contratos = Contract::where('user_1_id', $userId)
            ->orWhere('user_2_id', $userId)
            ->with(['exchange', 'user1', 'user2'])
            ->latest()
            ->paginate(15);

        setPaginationPrefix($contratos, '/api/contracts');

        return response()->json($contratos);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $contrato = Contract::with(['exchange', 'user1', 'user2'])->findOrFail($id);

        $denied = $this->authorizeContractAccess($contrato, $request->user()->id);
        if ($denied) return $denied;

        return response()->json($contrato);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $contrato = Contract::findOrFail($id);

        $denied = $this->authorizeContractAccess($contrato, $request->user()->id, false);
        if ($denied) return $denied;

        $request->validate([
            'observacao' => 'nullable|string|max:500',
        ]);

        $data = $request->only(['observacao']);
        if (isset($data['observacao'])) {
            $data['observacao'] = sanitizeString($data['observacao']);
        }

        $contrato->update($data);

        return response()->json($contrato);
    }

    public function sign(Request $request, $id): JsonResponse
    {
        return DB::transaction(function () use ($request, $id) {
            $contrato = Contract::lockForUpdate()->findOrFail($id);
            $userId = $request->user()->id;

            $denied = $this->authorizeContractAccess($contrato, $userId, false);
            if ($denied) return $denied;

            if (in_array($contrato->status, ['cancelled', 'rejected'])) {
                return response()->json(['message' => 'Este contrato não pode ser assinado.'], 422);
            }

            $exchange = $contrato->exchange()->first();
            if ($exchange && in_array($exchange->status, ['cancelled', 'completed'])) {
                return response()->json(['message' => 'A troca associada não está disponível.'], 422);
            }

            $now = now();

            if ($contrato->user_1_id === $userId && ! $contrato->assinatura_1_at) {
                $contrato->forceFill(['assinatura_1_at' => $now])->save();
            } elseif ($contrato->user_2_id === $userId && ! $contrato->assinatura_2_at) {
                $contrato->forceFill(['assinatura_2_at' => $now])->save();
            } else {
                return response()->json(['message' => 'Você já assinou este contrato.'], 422);
            }

            $contrato->refresh();
            if ($contrato->assinatura_1_at && $contrato->assinatura_2_at) {
                $contrato->forceFill(['status' => 'signed'])->save();
                $contrato->exchange()->update(['status' => 'completed']);
            } else {
                $contrato->forceFill(['status' => 'pending'])->save();
            }

            return response()->json($contrato->load(['exchange', 'user1', 'user2']));
        });
    }

    public function generatePdf(Request $request, $id): JsonResponse
    {
        $contrato = Contract::findOrFail($id);

        $denied = $this->authorizeContractAccess($contrato, $request->user()->id);
        if ($denied) return $denied;

        if ($contrato->status !== 'signed') {
            return response()->json(['message' => 'O contrato deve estar assinado para gerar o PDF.'], 422);
        }

        \App\Jobs\GenerateContractPdf::dispatch($contrato);

        return response()->json(['message' => 'Geração do PDF iniciada.']);
    }

    public function downloadPdf(Request $request, $id): JsonResponse
    {
        $contrato = Contract::with('exchange')->findOrFail($id);

        $denied = $this->authorizeContractAccess($contrato, $request->user()->id);
        if ($denied) return $denied;

        if (! $contrato->pdf_path) {
            return response()->json(['message' => 'PDF não disponível.'], 404);
        }

        $safePath = basename($contrato->pdf_path);
        $path = storage_path('app/contratos/' . $safePath);
        if (! file_exists($path)) {
            $path = storage_path('app/public/contratos/' . $safePath);
        }

        if (! file_exists($path)) {
            return response()->json(['message' => 'Arquivo não encontrado.'], 404);
        }

        return response()->download($path, $safePath);
    }
}
