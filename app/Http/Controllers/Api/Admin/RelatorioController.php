<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exchange;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RelatorioController extends Controller
{
    public function index(): JsonResponse
    {
        // Consolida contagens em queries únicas por tabela (evita 8 queries separadas)
        $usuarios = User::selectRaw('
            COUNT(*) as total,
            SUM(status = "approved") as aprovados,
            SUM(status = "pending") as pendentes
        ')->first();

        $servicos = Service::selectRaw('
            COUNT(*) as total,
            SUM(status = "active") as ativos
        ')->first();

        $trocas = Exchange::selectRaw('
            COUNT(*) as total,
            SUM(status = "confirmed") as confirmadas,
            SUM(status = "completed") as concluidas
        ')->first();

        $servicosPorCategoria = Service::select('categoria', DB::raw('count(*) as total'))
            ->groupBy('categoria')
            ->orderByDesc('total')
            ->get();

        // Usa GROUP BY SQL em vez de carregar todos os registros para PHP
        $usuariosPorMes = User::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as mes, count(*) as total")
            ->groupBy('mes')
            ->orderByDesc('mes')
            ->limit(12)
            ->get();

        $servicosPorMes = Service::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as mes, count(*) as total")
            ->groupBy('mes')
            ->orderByDesc('mes')
            ->limit(12)
            ->get();

        return response()->json([
            'resumo' => [
                'total_usuarios' => $usuarios->total,
                'usuarios_aprovados' => $usuarios->aprovados,
                'usuarios_pendentes' => $usuarios->pendentes,
                'total_servicos' => $servicos->total,
                'servicos_ativos' => $servicos->ativos,
                'total_trocas' => $trocas->total,
                'trocas_confirmadas' => $trocas->confirmadas,
                'trocas_concluidas' => $trocas->concluidas,
            ],
            'servicos_por_categoria' => $servicosPorCategoria,
            'usuarios_por_mes' => $usuariosPorMes,
            'servicos_por_mes' => $servicosPorMes,
        ]);
    }
}
