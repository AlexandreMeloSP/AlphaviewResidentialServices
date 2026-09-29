<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApprovedMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        if (! $user->email_verified_at) {
            return response()->json([
                'message' => 'Email não verificado. Verifique sua caixa de entrada.',
                'requires_verification' => true,
            ], 403);
        }

        if ($user->status !== 'approved') {
            return response()->json([
                'message' => 'Conta aguardando aprovação do administrador. Você pode visualizar serviços, mas não pode criar, editar ou enviar mensagens.',
                'requires_approval' => true,
            ], 403);
        }

        return $next($request);
    }
}
