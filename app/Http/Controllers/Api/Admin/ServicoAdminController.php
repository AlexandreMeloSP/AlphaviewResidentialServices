<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServicoAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Service::with(['user:id,name', 'user.profile:id,user_id,avatar'])->latest();

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('busca') && $request->busca !== '') {
            $busca = str_replace(['%', '_'], ['\\%', '\\_'], $request->busca);
            $query->where(function ($q) use ($busca) {
                $q->where('titulo', 'like', "%{$busca}%")
                    ->orWhere('categoria', 'like', "%{$busca}%");
            });
        }

        if ($request->has('user_id') && $request->user_id !== '') {
            $query->where('user_id', $request->user_id);
        }

        $servicos = $query->paginate(15);

        // Fix pagination URLs to include route prefix
        $prefix = config('app.route_prefix');
        if ($prefix) {
            $servicos->setPath("/{$prefix}/api/admin/servicos");
        }

        return response()->json($servicos);
    }

    public function toggleStatus($id): JsonResponse
    {
        $servico = Service::findOrFail($id);

        $novoStatus = $servico->status === 'active' ? 'inactive' : 'active';
        $servico->forceFill(['status' => $novoStatus])->save();

        return response()->json([
            'message' => 'Status do serviço atualizado.',
            'servico' => $servico->load('user:id,name'),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $servico = Service::findOrFail($id);
        $servico->delete();

        return response()->json(['message' => 'Serviço removido com sucesso.']);
    }
}
