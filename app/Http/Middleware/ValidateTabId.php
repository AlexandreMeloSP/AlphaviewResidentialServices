<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ValidateTabId
{
    public function handle(Request $request, Closure $next)
    {
        $sessionTabId = Session::get('tab_id');

        if (! $sessionTabId) {
            $sessionTabId = Str::uuid()->toString();
            Session::put('tab_id', $sessionTabId);
        }

        $tabId = $request->header('X-TAB-ID');

        if (! $tabId || $tabId !== $sessionTabId) {
            Session::flush();
            return response()->json(['message' => 'Sessão expirou. Abra uma nova aba para continuar.'], 401);
        }

        return $next($request);
    }
}
