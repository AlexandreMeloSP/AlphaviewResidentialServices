<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Admin\UserAdminController;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Mail\AdminApprovalRequestMail;
use App\Mail\MfaCodeMail;
use App\Mail\VerifyEmailMail;
use App\Mail\ResetPasswordMail;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private function appUrl(string $path = ''): string
    {
        $prefix = config('app.route_prefix') ?: 'alphaview';
        return url("/{$prefix}{$path}");
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $token = Str::random(64);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->save();

        $user->forceFill([
            'cpf' => RegisterRequest::sanitizarCpf($request->cpf),
            'status' => 'pending',
            'email_verification_token' => $token,
            'accepted_terms_at' => now(),
            'terms_version' => '1.0',
        ])->save();

        (new Profile())->forceFill(['user_id' => $user->id])->save();

        $url = $this->appUrl("/api/auth/verify-email/{$token}");
        Mail::to($user->email)->send(new VerifyEmailMail($user, $url));

        return response()->json([
            'message' => 'Conta criada com sucesso! Verifique seu email para ativar sua conta.',
        ], 201);
    }

    public function verifyEmail(string $token): JsonResponse|\Illuminate\View\View
    {
        $user = User::where('email_verification_token', $token)
            ->whereNull('email_verified_at')
            ->first();

        if (! $user) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Link de verificação inválido ou já utilizado.'], 422);
            }
            return view('verify-email', ['success' => false, 'message' => 'Link de verificação inválido ou já utilizado.']);
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'email_verification_token' => null,
        ])->save();

        // Send admin approval request email
        $token = UserAdminController::generateApprovalToken($user);
        $approveUrl = $this->appUrl("/api/admin/approve-user/{$user->id}/{$token}");
        $rejectUrl = $this->appUrl("/api/admin/reject-user/{$user->id}/{$token}");

        // Send to admin(s)
        $admins = User::where('is_admin', true)->pluck('email');
        foreach ($admins as $adminEmail) {
            try {
                Mail::to($adminEmail)->send(new AdminApprovalRequestMail($user, $approveUrl, $rejectUrl));
            } catch (\Throwable $e) {
                \Log::error('Falha ao enviar email de aprovação para admin: ' . $e->getMessage());
            }
        }

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Email verificado com sucesso! Aguarde aprovação do administrador.']);
        }
        return view('verify-email', ['success' => true, 'message' => 'Email verificado com sucesso! Aguarde aprovação do administrador.']);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $email = strtolower($request->email);
        $lockKey = 'login-lock:' . $email;
        $ipLockKey = 'login-ip-lock:' . $request->ip();

        // Rate limit por IP (10 tentativas a cada 5 min)
        if (RateLimiter::tooManyAttempts($ipLockKey, 10)) {
            $seconds = RateLimiter::availableIn($ipLockKey);
            return response()->json([
                'message' => "Muitas tentativas deste IP. Tente novamente em {$seconds} segundos.",
            ], 429);
        }

        // Rate limit por email (5 tentativas)
        if (RateLimiter::tooManyAttempts($lockKey, 5)) {
            $seconds = RateLimiter::availableIn($lockKey);
            return response()->json([
                'message' => "Conta bloqueada temporariamente. Tente novamente em {$seconds} segundos.",
            ], 429);
        }

        $user = User::where('email', $email)->first();

        if (! $user || ! $user->verifyPassword($request->password)) {
            RateLimiter::hit($lockKey, 300);
            RateLimiter::hit($ipLockKey, 300);
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        RateLimiter::clear($lockKey);
        RateLimiter::clear($ipLockKey);

        if (! $user->email_verified_at) {
            return response()->json([
                'message' => 'Credenciais inválidas.',
            ], 422);
        }

        if ($user->status === 'rejected') {
            return response()->json([
                'message' => 'Seu cadastro foi rejeitado. Entre em contato com o suporte.',
            ], 403);
        }

        if ($user->mfa_enabled) {
            $code = str_pad((string) random_int(100000, 999999), 6, '0');
            $user->forceFill([
                'mfa_code' => Hash::make($code),
                'mfa_code_expires_at' => now()->addMinutes(5),
            ])->save();

            $request->session()->put('mfa_user_id', $user->id);

            Mail::to($user->email)->send(new MfaCodeMail($user, $code));

            return response()->json([
                'message' => 'Código de verificação enviado para seu email.',
                'requires_mfa' => true,
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        $tabId = \Illuminate\Support\Str::uuid()->toString();
        $request->session()->put('tab_id', $tabId);

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        return response()->json([
            'user' => $user->only(['id', 'name', 'email', 'status', 'mfa_enabled']),
            'tab_id' => $tabId,
        ]);
    }

    public function verifyMfa(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $userId = $request->session()->get('mfa_user_id');
        if (! $userId) {
            return response()->json(['message' => 'Sessão MFA expirada. Faça login novamente.'], 422);
        }

        $lockKey = 'mfa-lock:' . $userId;
        if (RateLimiter::tooManyAttempts($lockKey, 5)) {
            $seconds = RateLimiter::availableIn($lockKey);
            return response()->json([
                'message' => "Muitas tentativas. Tente novamente em {$seconds} segundos.",
            ], 429);
        }

        $user = User::findOrFail($userId);

        if (! $user->mfa_enabled) {
            return response()->json(['message' => 'MFA não está habilitado para esta conta.'], 422);
        }

        if (! $user->mfa_code || ! $user->mfa_code_expires_at) {
            return response()->json(['message' => 'Nenhum código MFA pendente. Faça login novamente.'], 422);
        }

        if ($user->mfa_code_expires_at->isPast()) {
            return response()->json(['message' => 'Código expirado. Solicite um novo login.'], 422);
        }

        if (! Hash::check((string) $request->code, $user->mfa_code)) {
            RateLimiter::hit($lockKey, 300);
            return response()->json(['message' => 'Código incorreto inserido.'], 422);
        }

        RateLimiter::clear($lockKey);

        $user->forceFill([
            'mfa_code' => null,
            'mfa_code_expires_at' => null,
        ])->save();

        $request->session()->forget('mfa_user_id');

        Auth::login($user);
        $request->session()->regenerate();

        $tabId = \Illuminate\Support\Str::uuid()->toString();
        $request->session()->put('tab_id', $tabId);

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        return response()->json([
            'user' => $user->only(['id', 'name', 'email', 'status', 'mfa_enabled']),
            'tab_id' => $tabId,
        ]);
    }

    public function requestMfaCode(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->mfa_code_expires_at && $user->mfa_code_expires_at->gt(now()->subSeconds(60))) {
            return response()->json(['message' => 'Aguarde 60 segundos antes de solicitar um novo código.'], 429);
        }

        $code = str_pad((string) random_int(100000, 999999), 6, '0');
        $user->forceFill([
            'mfa_code' => Hash::make($code),
            'mfa_code_expires_at' => now()->addMinutes(5),
        ])->save();

        try {
            Mail::to($user->email)->send(new MfaCodeMail($user, $code));
        } catch (\Throwable $e) {
            \Log::error('Falha ao enviar código MFA: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao enviar código para o e-mail.'], 500);
        }

        return response()->json(['message' => 'Código de verificação enviado para seu email.']);
    }

    public function toggleMfa(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->mfa_enabled) {
            $request->validate([
                'password' => ['required', 'string'],
                'code' => ['nullable', 'digits:6'],
            ]);

            if (! $user->verifyPassword($request->password)) {
                return response()->json(['message' => 'Senha incorreta.'], 422);
            }

            // Se nenhum código foi informado, gera e envia automaticamente
            if (! $request->filled('code')) {
                $code = str_pad((string) random_int(100000, 999999), 6, '0');
                $user->forceFill([
                    'mfa_code' => Hash::make($code),
                    'mfa_code_expires_at' => now()->addMinutes(5),
                ])->save();

                try {
                    Mail::to($user->email)->send(new MfaCodeMail($user, $code));
                } catch (\Throwable $e) {
                    \Log::error('Falha ao enviar código MFA para desativação: ' . $e->getMessage());
                    return response()->json(['message' => 'Erro ao enviar código de verificação para o e-mail.'], 500);
                }

                return response()->json([
                    'message' => 'Código de verificação enviado para seu email. Digite-o para confirmar a desativação.',
                    'requires_code' => true,
                ]);
            }

            if (! $user->mfa_code || ! $user->mfa_code_expires_at) {
                return response()->json(['message' => 'Nenhum código MFA pendente. Solicite um novo código.'], 422);
            }

            if ($user->mfa_code_expires_at->isPast()) {
                return response()->json(['message' => 'Código expirado. Solicite um novo código.'], 422);
            }

            if (! Hash::check((string) $request->code, $user->mfa_code)) {
                return response()->json(['message' => 'Código MFA incorreto.'], 422);
            }

            $user->forceFill([
                'mfa_enabled' => false,
                'mfa_code' => null,
                'mfa_code_expires_at' => null,
            ])->save();

            return response()->json([
                'message' => 'Autenticação de dois fatores desativada com sucesso.',
                'mfa_enabled' => false,
            ]);
        }

        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (! $user->verifyPassword($request->password)) {
            return response()->json(['message' => 'Senha incorreta.'], 422);
        }

        $user->forceFill(['mfa_enabled' => true])->save();

        return response()->json([
            'message' => 'Autenticação de dois fatores ativada.',
            'mfa_enabled' => true,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Sessão encerrada com sucesso.']);
    }

    public function user(Request $request): JsonResponse
    {
        $user = $request->user()->load('profile');
        $data = $user->only([
            'id',
            'name',
            'email',
            'cpf',
            'status',
            'mfa_enabled',
            'email_verified_at',
        ]) + ['profile' => $user->profile];
        if ($user->isAdmin()) {
            $data['is_admin'] = true;
        }
        return response()->json($data);
    }

    public function resendVerification(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $request->email)->first();

        if (! $user || $user->email_verified_at) {
            return response()->json(['message' => 'Se o email estiver cadastrado e não verificado, um novo link foi enviado.']);
        }

        $token = Str::random(64);
        $user->forceFill(['email_verification_token' => $token])->save();

        $url = $this->appUrl("/api/auth/verify-email/{$token}");
        Mail::to($user->email)->send(new VerifyEmailMail($user, $url));

        return response()->json(['message' => 'Novo email de verificação enviado.']);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $email = $request->email;
        $key = 'password-reset:' . strtolower($email);

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => "Muitas tentativas. Tente novamente em {$seconds} segundos.",
            ], 429);
        }

        RateLimiter::hit($key, 60);

        $user = User::where('email', $email)->first();

        if ($user) {
            $token = Str::random(64);
            \DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                ['token' => Hash::make($token), 'created_at' => now()]
            );

            $url = $this->appUrl("/redefinir-senha/{$token}");
            Mail::to($user->email)->send(new ResetPasswordMail($user, $url));
        }

        // Always return the same response and delay to prevent timing-based enumeration
        usleep((int) (random_int(100000, 300000))); // 100-300ms random delay
        return response()->json(['message' => 'Se o email estiver cadastrado, você receberá um link para redefinir sua senha.']);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()->mixedCase()->symbols()],
        ], [
            'password.required' => 'A senha é obrigatória.',
            'password.confirmed' => 'A confirmação de senha não confere.',
            'password.min.string' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.letters' => 'A senha deve conter letras.',
            'password.numbers' => 'A senha deve conter números.',
            'password.mixed' => 'A senha deve conter maiúsculas e minúsculas.',
            'password.symbols' => 'A senha deve conter caracteres especiais.',
        ]);

        return \DB::transaction(function () use ($request) {
            $record = \DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->lockForUpdate()
                ->first();

            if (! $record || ! Hash::check($request->token, $record->token)) {
                return response()->json(['message' => 'Token inválido ou expirado.'], 422);
            }

            if (now()->diffInMinutes($record->created_at) > 60) {
                \DB::table('password_reset_tokens')->where('email', $request->email)->delete();
                return response()->json(['message' => 'Token expirado. Solicite um novo.'], 422);
            }

            $user = User::where('email', $request->email)->first();
            if (! $user) {
                return response()->json(['message' => 'Usuário não encontrado.'], 422);
            }

            $user->update(['password' => $request->password]);
            \DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            \DB::table('sessions')->where('user_id', $user->id)->delete();

            return response()->json(['message' => 'Senha redefinida com sucesso!']);
        });
    }
}
