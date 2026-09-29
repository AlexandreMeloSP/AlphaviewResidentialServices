<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Exchange;
use App\Models\Message;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        $totalServicosConcluidos = Exchange::where('status', 'completed')->count();
        $totalUsuarios = User::where('status', 'approved')->count();
        $totalServicosAtivos = Service::where('status', 'active')->count();

        $servicosPorCategoria = Service::where('status', 'active')
            ->select('categoria', DB::raw('count(*) as total'))
            ->groupBy('categoria')
            ->orderByDesc('total')
            ->get();

        // Usa GROUP BY SQL em vez de carregar todos os registros para PHP
        $trocasPorMes = Exchange::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as mes, count(*) as total")
            ->groupBy('mes')
            ->orderByDesc('mes')
            ->limit(12)
            ->get();

        $servicosRecentes = Service::with(['user:id,name', 'user.profile:id,user_id,avatar'])
            ->where('status', 'active')
            ->latest()
            ->limit(6)
            ->get();

        $usuariosRecentes = User::select('id', 'name', 'created_at')
            ->where('status', 'approved')
            ->latest()
            ->limit(6)
            ->get();

        return response()->json([
            'total_servicos_concluidos' => $totalServicosConcluidos,
            'total_usuarios' => $totalUsuarios,
            'total_servicos_ativos' => $totalServicosAtivos,
            'servicos_por_categoria' => $servicosPorCategoria,
            'trocas_por_mes' => $trocasPorMes,
            'servicos_recentes' => $servicosRecentes,
            'usuarios_recentes' => $usuariosRecentes,
        ]);
    }

    public function userStats(Request $request): JsonResponse
    {
        $user = $request->user();

        $meusServicos = Service::where('user_id', $user->id)->where('status', 'active')->count();
        $servicosRestantes = max(0, 5 - $meusServicos);

        // Usa SQL COUNT(DISTINCT) em vez de carregar todas as mensagens para PHP
        $mensagensCount = Message::where(function ($q) use ($user) {
            $q->where('sender_id', $user->id)
              ->orWhere('receiver_id', $user->id);
        })
            ->selectRaw('COUNT(DISTINCT CASE WHEN sender_id = ? THEN receiver_id WHEN receiver_id = ? THEN sender_id END) as total', [$user->id, $user->id])
            ->value('total');

        $servicosRecentes = Service::with(['user:id,name', 'user.profile:id,user_id,avatar'])
            ->where('status', 'active')
            ->latest()
            ->limit(6)
            ->get();

        $contratosCount = Contract::where('user_1_id', $user->id)
            ->orWhere('user_2_id', $user->id)
            ->count();

        $trocasCount = Exchange::where('user_proponente_id', $user->id)
            ->orWhere('user_receptor_id', $user->id)
            ->count();

        return response()->json([
            'meus_servicos' => $meusServicos,
            'servicos_restantes' => $servicosRestantes,
            'mensagens_count' => $mensagensCount,
            'trocas_count' => $trocasCount,
            'contratos_count' => $contratosCount,
            'servicos_recentes' => $servicosRecentes,
        ]);
    }
}
