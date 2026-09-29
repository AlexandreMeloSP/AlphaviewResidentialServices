<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmailMail;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $profile = Profile::where('user_id', $request->user()->id)->firstOrFail();

        return response()->json($profile->load('user'));
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => ['sometimes', 'string', 'max:255', 'regex:/^[a-zA-ZÀ-ÿ\s]+$/'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'current_password' => ['nullable', 'string'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'bio' => ['nullable', 'string', 'max:500'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'endereco' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->filled('email') && strtolower($request->email) !== strtolower($user->email)) {
            if (! $request->filled('current_password')) {
                return response()->json(['message' => 'A senha atual é obrigatória para alterar o e-mail.'], 422);
            }
        }

        $emailChanged = false;
        if ($request->filled('email') && strtolower($request->email) !== strtolower($user->email)) {
            if (! $user->verifyPassword((string) $request->current_password)) {
                return response()->json(['message' => 'Senha atual incorreta.'], 422);
            }
            $emailChanged = true;
        }

        $data = [];
        if ($request->filled('name')) {
            $data['name'] = sanitizeString($request->name);
        }
        if ($emailChanged) {
            $data['email'] = strtolower($request->email);
        }

        if (! empty($data)) {
            $user->update($data);
        }

        if ($emailChanged) {
            $token = Str::random(64);
            $user->forceFill([
                'email_verified_at' => null,
                'email_verification_token' => $token,
            ])->save();

            $prefix = config('app.route_prefix') ?: 'alphaview';
            $url = url("/{$prefix}/api/auth/verify-email/{$token}");
            try {
                Mail::to($user->email)->send(new VerifyEmailMail($user, $url));
            } catch (\Throwable $e) {
                \Log::error('Falha ao enviar email de verificação após troca de email: ' . $e->getMessage());
            }
        }

        $profile = Profile::where('user_id', $user->id)->first();
        if (! $profile) {
            $profile = (new Profile())->forceFill(['user_id' => $user->id]);
            $profile->save();
        }

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $error = validateImageFile($file);
            if ($error) return $error;

            if ($profile->avatar && Storage::disk('public')->exists($profile->avatar)) {
                Storage::disk('public')->delete($profile->avatar);
            }

            $profile->update([
                'avatar' => $file->store('avatars', 'public'),
            ]);
        }

        $profileData = $request->only(['bio', 'telefone', 'endereco']);
        if (! empty($profileData)) {
            foreach (['bio', 'endereco'] as $field) {
                if (! empty($profileData[$field])) {
                    $profileData[$field] = sanitizeString($profileData[$field]);
                }
            }
            $profile->update($profileData);
        }

        $user->refresh();

        return response()->json($user->load('profile'));
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()->mixedCase()->symbols()],
        ]);

        $user = $request->user();

        if (! $user->verifyPassword($request->current_password)) {
            return response()->json(['message' => 'Senha atual incorreta.'], 422);
        }

        $user->update([
            'password' => $request->password,
        ]);

        \DB::table('password_reset_tokens')->where('email', $user->email)->delete();
        \DB::table('sessions')->where('user_id', $user->id)
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        return response()->json(['message' => 'Senha atualizada com sucesso.']);
    }

    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $user = $request->user();
        $profile = Profile::where('user_id', $user->id)->first();
        if (! $profile) {
            $profile = (new Profile())->forceFill(['user_id' => $user->id]);
            $profile->save();
        }

        $file = $request->file('avatar');
        $error = validateImageFile($file);
        if ($error) return $error;

        if ($profile->avatar && Storage::disk('public')->exists($profile->avatar)) {
            Storage::disk('public')->delete($profile->avatar);
        }

        $profile->update([
            'avatar' => $file->store('avatars', 'public'),
        ]);

        return response()->json(['message' => 'Avatar atualizado.', 'avatar' => $profile->avatar]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = $request->user();

        if (! $user->verifyPassword($request->password)) {
            return response()->json(['message' => 'Senha incorreta.'], 422);
        }

        $user->delete();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Conta desativada. Será excluída permanentemente em 7 dias.',
        ]);
    }
}
