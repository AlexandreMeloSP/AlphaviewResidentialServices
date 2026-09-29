<?php

use App\Http\Controllers\Api\Admin\RelatorioController;
use App\Http\Controllers\Api\Admin\ServicoAdminController;
use App\Http\Controllers\Api\Admin\UserAdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExchangeController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ServicoController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

// Autenticação pública
Route::middleware([
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
])->group(function () {
    // Contato público
    Route::post('/contato', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'assunto' => ['required', 'string', 'max:255'],
            'mensagem' => ['required', 'string', 'max:2000'],
        ]);

        $assunto = preg_replace('/[\r\n]+/', '', strip_tags($request->assunto));
        $nome = preg_replace('/[\r\n]+/', '', strip_tags($request->nome));
        $mensagem = preg_replace('/[\r\n]+/', '', strip_tags($request->mensagem));

        try {
            \Illuminate\Support\Facades\Mail::to(config('app.contact_email'))->send(
                new \App\Mail\ContactMail(
                    $nome,
                    $request->email,
                    $assunto,
                    $mensagem
                )
            );
        } catch (\Throwable $e) {
            \Log::error('Falha ao enviar email de contato: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao enviar mensagem. Tente novamente mais tarde.'], 500);
        }

        return response()->json(['message' => 'Mensagem enviada com sucesso!']);
    })->middleware('throttle:5,1');

    Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1');
    Route::get('/auth/verify-email/{token}', [AuthController::class, 'verifyEmail'])->middleware('throttle:10,1');
    Route::post('/auth/resend-verification', [AuthController::class, 'resendVerification'])->middleware('throttle:3,1');
    Route::post('/auth/verify-mfa', [AuthController::class, 'verifyMfa']);

    // Admin approval/rejection via email link (no auth required, secret token protects)
    Route::get('/admin/approve-user/{id}/{token}', [UserAdminController::class, 'approveViaLink'])->middleware('throttle:3,1');
    Route::get('/admin/reject-user/{id}/{token}', [UserAdminController::class, 'rejectViaLink'])->middleware('throttle:3,1');
});

// Usuários públicos (apenas aprovados) - requer autenticação
Route::middleware([
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
    'auth:web',
    'approved',
])->get('/usuarios', function () {
    $usuarios = \App\Models\User::where('status', 'approved')
        ->select('id', 'name', 'created_at')
        ->with(['profile' => function ($query) {
            $query->select('id', 'user_id', 'avatar');
        }])
        ->withCount('services')
        ->latest()
        ->get();

    return response()->json(['data' => $usuarios]);
})->middleware('throttle:30,1');

// Autenticadas
Route::middleware([
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
    'auth:web',
    'tab-id',
    'throttle:60,1',
])->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/auth/toggle-mfa', [AuthController::class, 'toggleMfa'])->middleware('throttle:5,1');
    Route::post('/auth/request-mfa-code', [AuthController::class, 'requestMfaCode'])->middleware('throttle:5,1');

    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->middleware('throttle:30,1');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->middleware('throttle:5,1');
    Route::delete('/profile', [ProfileController::class, 'destroy']);

    // Dashboard do usuário
    Route::get('/dashboard/user', [DashboardController::class, 'userStats']);

    // Serviços (apenas aprovados podem criar/editar/excluir)
    Route::get('/servicos', [ServicoController::class, 'index'])->middleware('throttle:30,1');
    Route::get('/servicos/mine', [ServicoController::class, 'mine'])->middleware('approved');
    Route::get('/servicos/{id}', [ServicoController::class, 'show'])->middleware('approved');
    Route::post('/servicos', [ServicoController::class, 'store'])
        ->middleware('approved');
    Route::put('/servicos/{id}', [ServicoController::class, 'update'])
        ->middleware('approved');
    Route::delete('/servicos/{id}', [ServicoController::class, 'destroy'])
        ->middleware('approved');
    Route::patch('/servicos/{id}/toggle-status', [ServicoController::class, 'toggleStatus'])
        ->middleware('approved');

    // Mensagens (apenas aprovados)
    Route::get('/messages/conversations', [MessageController::class, 'conversations'])
        ->middleware('approved');
    Route::get('/messages/conversation/{id}', [MessageController::class, 'conversation'])
        ->middleware('approved');
    Route::post('/messages', [MessageController::class, 'store'])
        ->middleware(['approved', 'throttle:20,1']);
    Route::delete('/messages/{id}', [MessageController::class, 'destroy'])
        ->middleware('approved');

    // Trocas (apenas aprovados)
    Route::get('/exchanges', [ExchangeController::class, 'index'])
        ->middleware('approved');
    Route::post('/exchanges', [ExchangeController::class, 'store'])
        ->middleware('approved');
    Route::get('/exchanges/{id}', [ExchangeController::class, 'show'])
        ->middleware('approved');
    Route::post('/exchanges/{id}/confirm', [ExchangeController::class, 'confirm'])
        ->middleware('approved');
    Route::post('/exchanges/{id}/cancel', [ExchangeController::class, 'cancel'])
        ->middleware('approved');

    // Contratos (apenas aprovados)
    Route::get('/contracts', [ContractController::class, 'index'])
        ->middleware('approved');
    Route::get('/contracts/{id}', [ContractController::class, 'show'])
        ->middleware('approved');
    Route::put('/contracts/{id}', [ContractController::class, 'update'])
        ->middleware('approved');
    Route::post('/contracts/{id}/sign', [ContractController::class, 'sign'])
        ->middleware('approved');
    Route::post('/contracts/{id}/pdf', [ContractController::class, 'generatePdf'])
        ->middleware('approved');
    Route::get('/contracts/{id}/download', [ContractController::class, 'downloadPdf'])
        ->middleware('approved');

    // Admin
    Route::middleware([AdminMiddleware::class])->prefix('admin')->group(function () {
        Route::get('/relatorios', [RelatorioController::class, 'index']);
        Route::get('/usuarios', [UserAdminController::class, 'index']);
        Route::get('/usuarios/{id}', [UserAdminController::class, 'show']);
        Route::post('/usuarios/{id}/approve', [UserAdminController::class, 'approve']);
        Route::post('/usuarios/{id}/reject', [UserAdminController::class, 'reject']);
        Route::post('/usuarios/{id}/toggle-admin', [UserAdminController::class, 'toggleAdmin']);
        Route::delete('/usuarios/{id}', [UserAdminController::class, 'destroy']);
        Route::post('/usuarios/{id}/restore', [UserAdminController::class, 'restore']);
        Route::get('/servicos', [ServicoAdminController::class, 'index']);
        Route::post('/servicos/{id}/toggle-status', [ServicoAdminController::class, 'toggleStatus']);
        Route::delete('/servicos/{id}', [ServicoAdminController::class, 'destroy']);
    });
});
