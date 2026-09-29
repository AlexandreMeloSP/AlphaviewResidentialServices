<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminApprovalRequestMail;
use App\Mail\UserApprovedMail;
use App\Mail\UserRejectedMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserAdminController extends Controller
{
    private function appUrl(string $path = ''): string
    {
        $prefix = config('app.route_prefix') ?: 'alphaview';
        return url("/{$prefix}{$path}");
    }

    public function index(Request $request): JsonResponse
    {
        $query = User::with('profile');

        // Filtrar excluídos
        if ($request->boolean('deleted')) {
            $query->onlyTrashed();
        } else {
            $query->whereNull('deleted_at');
        }

        // Filtrar por status (apenas para não-excluídos)
        if (! $request->boolean('deleted') && $request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('busca') && $request->busca !== '') {
            $busca = str_replace(['%', '_'], ['\\%', '\\_'], $request->busca);
            $query->where(function ($q) use ($busca) {
                $q->where('name', 'like', "%{$busca}%")
                    ->orWhere('email', 'like', "%{$busca}%")
                    ->orWhere('cpf', 'like', "%{$busca}%");
            });
        }

        $perPage = min((int) $request->input('per_page', 15), 50);
        $usuarios = $query->latest()->paginate($perPage);

        // Fix pagination URLs to include route prefix
        $prefix = config('app.route_prefix');
        if ($prefix) {
            $usuarios->setPath("/{$prefix}/api/admin/usuarios");
        }

        $usuarios->getCollection()->each->makeVisible([
            'email',
            'is_admin',
            'cpf',
            'mfa_enabled',
            'accepted_terms_at',
            'terms_version',
            'deleted_at',
            'email_verified_at',
            'last_login_at',
            'last_login_ip',
            'created_at',
            'updated_at',
        ]);

        return response()->json($usuarios);
    }

    public function show($id): JsonResponse
    {
        $usuario = User::with('profile', 'services')->withTrashed()->findOrFail($id);

        return response()->json($usuario->only([
            'id', 'name', 'email', 'status', 'is_admin', 'mfa_enabled',
            'cpf', 'created_at', 'updated_at', 'deleted_at',
        ]) + [
            'profile' => $usuario->profile,
            'services' => $usuario->services,
        ]);
    }

    public function approve($id): JsonResponse
    {
        $usuario = User::findOrFail($id);

        if ($usuario->status === 'approved') {
            return response()->json(['message' => 'Usuário já está aprovado.'], 422);
        }

        $usuario->forceFill([
            'status' => 'approved',
            'email_verified_at' => $usuario->email_verified_at ?? now(),
        ])->save();

        // Send approval email to user
        try {
            Mail::to($usuario->email)->send(new UserApprovedMail($usuario, $this->appUrl('/entrar')));
        } catch (\Throwable $e) {
            \Log::error('Falha ao enviar email de aprovação: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Usuário aprovado com sucesso.',
            'user' => $usuario->only(['id', 'name', 'email', 'status', 'is_admin', 'cpf', 'created_at']),
        ]);
    }

    public function reject($id): JsonResponse
    {
        $usuario = User::findOrFail($id);

        if ($usuario->status === 'rejected') {
            return response()->json(['message' => 'Usuário já está rejeitado.'], 422);
        }

        $usuario->forceFill(['status' => 'rejected'])->save();

        // Send rejection email to user
        try {
            Mail::to($usuario->email)->send(new UserRejectedMail($usuario, $this->appUrl('/entrar')));
        } catch (\Throwable $e) {
            \Log::error('Falha ao enviar email de rejeição: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Usuário rejeitado.',
            'user' => $usuario->only(['id', 'name', 'email', 'status', 'is_admin', 'cpf', 'created_at']),
        ]);
    }

    private function validateApprovalToken(User $usuario, string $token): bool
    {
        if (! str_contains($token, '.')) {
            return false;
        }

        [$expires, $hash] = explode('.', $token, 2);
        if ((int) $expires < now()->timestamp) {
            return false;
        }
        $expected = hash_hmac('sha256', $usuario->id . $usuario->email . $expires, config('app.key'));
        return hash_equals($expected, $hash);
    }

    public function approveViaLink($id, $token)
    {
        $usuario = User::findOrFail($id);

        if (! $this->validateApprovalToken($usuario, (string) $token)) {
            return view('admin-approval', ['usuario' => null, 'error' => 'Link inválido ou expirado.', 'already' => null, 'action' => null, 'token' => null, 'base' => config('app.route_prefix') ? '/' . config('app.route_prefix') : '']);
        }

        if ($usuario->status === 'approved') {
            return view('admin-approval', ['usuario' => $usuario, 'error' => null, 'already' => 'Usuário já está aprovado.', 'action' => null, 'token' => null, 'base' => config('app.route_prefix') ? '/' . config('app.route_prefix') : '']);
        }

        return view('admin-approval', ['usuario' => $usuario, 'error' => null, 'already' => null, 'action' => 'approve', 'token' => $token, 'base' => config('app.route_prefix') ? '/' . config('app.route_prefix') : '']);
    }

    public function rejectViaLink($id, $token)
    {
        $usuario = User::findOrFail($id);

        if (! $this->validateApprovalToken($usuario, (string) $token)) {
            return view('admin-approval', ['usuario' => null, 'error' => 'Link inválido ou expirado.', 'already' => null, 'action' => null, 'token' => null, 'base' => config('app.route_prefix') ? '/' . config('app.route_prefix') : '']);
        }

        if ($usuario->status === 'rejected') {
            return view('admin-approval', ['usuario' => $usuario, 'error' => null, 'already' => 'Usuário já está rejeitado.', 'action' => null, 'token' => null, 'base' => config('app.route_prefix') ? '/' . config('app.route_prefix') : '']);
        }

        return view('admin-approval', ['usuario' => $usuario, 'error' => null, 'already' => null, 'action' => 'reject', 'token' => $token, 'base' => config('app.route_prefix') ? '/' . config('app.route_prefix') : '']);
    }

    public static function generateApprovalToken(User $user, ?int $expires = null): string
    {
        $expires = $expires ?? now()->addHours(24)->timestamp;
        $hash = hash_hmac('sha256', $user->id . $user->email . $expires, config('app.key'));
        return "{$expires}.{$hash}";
    }

    public function toggleAdmin($id): JsonResponse
    {
        $usuario = User::findOrFail($id);

        if ($usuario->id === request()->user()->id) {
            return response()->json(['message' => 'Você não pode alterar seu próprio status de administrador.'], 422);
        }

        $usuario->forceFill(['is_admin' => !$usuario->is_admin])->save();

        return response()->json([
            'message' => $usuario->is_admin ? 'Usuário promovido a administrador.' : 'Privilégios de administrador removidos.',
            'user' => $usuario->only(['id', 'name', 'email', 'status', 'is_admin']),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $usuario = User::withTrashed()->findOrFail($id);

        if ($usuario->id === request()->user()->id) {
            return response()->json(['message' => 'Você não pode excluir sua própria conta.'], 422);
        }

        $usuario->cleanupRelatedData();
        $usuario->forceDelete();

        return response()->json(['message' => 'Usuário e todos os seus dados foram excluídos permanentemente.']);
    }

    public function restore($id): JsonResponse
    {
        $usuario = User::withTrashed()->findOrFail($id);

        if ($usuario->trashed()) {
            $usuario->restore();

            return response()->json([
                'message' => 'Usuário restaurado com sucesso.',
                'user' => $usuario->only(['id', 'name', 'email', 'status', 'is_admin', 'cpf', 'created_at']),
            ]);
        }

        if ($usuario->status === 'rejected') {
            $usuario->forceFill(['status' => 'pending'])->save();

            return response()->json([
                'message' => 'Usuário reativado com sucesso. Agora está pendente de aprovação.',
                'user' => $usuario->only(['id', 'name', 'email', 'status', 'is_admin', 'cpf', 'created_at']),
            ]);
        }

        return response()->json(['message' => 'Usuário já está ativo.'], 422);
    }
}
