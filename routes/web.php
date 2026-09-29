<?php

use Illuminate\Support\Facades\Route;

// Healthcheck
Route::get('/healthcheck', function () {
    return response()->json([
        'status' => 'ok',
    ]);
})->middleware('throttle:60,1');

// Sobrescreve a rota /up do framework (registrada antes de web.php) —
// a view padrão carrega CDNs externos (bunny.net, jsdelivr) bloqueados pelo CSP
Route::get('/up', fn () => response('OK', 200));

// Landing page
Route::get('/', function () {
    $stats = [
        'usuarios' => \App\Models\User::where('status', 'approved')->count(),
        'servicos' => \App\Models\Service::where('status', 'active')->count(),
        'trocas' => \App\Models\Exchange::where('status', 'completed')->count(),
    ];

    return view('landing', compact('stats'));
});

// Public pages
Route::view('/entrar', 'pages.entrar');
Route::view('/cadastrar', 'pages.cadastrar');
Route::view('/esqueci-senha', 'pages.esqueci-senha');
Route::get('/redefinir-senha/{token}', function (string $token) {
    return view('pages.redefinir-senha', ['token' => $token]);
});
Route::view('/sobre', 'pages.sobre');
Route::view('/contatos', 'pages.contatos');
Route::view('/politica-de-privacidade', 'pages.politica-de-privacidade');
Route::view('/termos-de-uso', 'pages.termos-de-uso');

Route::get('/usuarios', function () {
    $usuarios = \App\Models\User::withCount('services')
        ->where('status', 'approved')
        ->whereNotNull('email_verified_at')
        ->latest()
        ->get();

    return view('pages.usuarios', compact('usuarios'));
})->middleware('throttle:30,1');

Route::get('/servicos', function () {
    $servicos = \App\Models\Service::with('user')
        ->where('status', 'active')
        ->latest()
        ->paginate(12);

    return view('pages.servicos', compact('servicos'));
});

// React SPA catch-all — exclude api and sanctum paths
// Status 404 evita soft-404: rotas inexistentes não respondem 200 (a shell
// ainda carrega e o router do React trata o erro no cliente)
Route::get('/{any}', function () {
    return response()->view('app', [], 404);
})->where('any', '(?!api|sanctum).*');
