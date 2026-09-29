<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendMessageRequest;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MessageController extends Controller
{
    public function conversations(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $partnerIds = Message::where(function ($q) use ($userId) {
            $q->where('sender_id', $userId)->orWhere('receiver_id', $userId);
        })
            ->selectRaw('DISTINCT CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END as partner_id', [$userId])
            ->pluck('partner_id');

        $conversas = collect();
        $partners = User::with('profile')->whereIn('id', $partnerIds)->get()->keyBy('id');

        foreach ($partnerIds as $partnerId) {
            $user = $partners->get($partnerId);
            if (! $user) {
                continue;
            }

            $lastMessage = Message::where(function ($q) use ($userId, $partnerId) {
                $q->where('sender_id', $userId)->where('receiver_id', $partnerId);
            })->orWhere(function ($q) use ($userId, $partnerId) {
                $q->where('sender_id', $partnerId)->where('receiver_id', $userId);
            })->latest()->first();

            $unread = Message::where('sender_id', $partnerId)
                ->where('receiver_id', $userId)
                ->where('lida', false)
                ->count();

            $conversas->push([
                'user' => $user,
                'last_message' => $lastMessage,
                'unread_count' => $unread,
            ]);
        }

        $conversas = $conversas->sortByDesc('last_message.created_at')->values();

        return response()->json($conversas);
    }

    public function conversation(Request $request, $userId): JsonResponse
    {
        $currentUserId = $request->user()->id;

        $mensagens = Message::where(function ($q) use ($currentUserId, $userId) {
            $q->where('sender_id', $currentUserId)
                ->where('receiver_id', $userId);
        })->orWhere(function ($q) use ($currentUserId, $userId) {
            $q->where('sender_id', $userId)
                ->where('receiver_id', $currentUserId);
        })->orderBy('created_at', 'asc')
            ->limit(50)
            ->with(['sender', 'receiver'])
            ->get();

        // Marcar como lidas
        Message::where('sender_id', $userId)
            ->where('receiver_id', $currentUserId)
            ->where('lida', false)
            ->update(['lida' => true]);

        return response()->json($mensagens);
    }

    public function store(SendMessageRequest $request): JsonResponse
    {
        Gate::authorize('create', Message::class);

        $senderId = $request->user()->id;
        $receiverId = $request->receiver_id;

        $receiver = User::findOrFail($receiverId);

        if (! $receiver->isApproved()) {
            return response()->json(['message' => 'O destinatário não está aprovado.'], 422);
        }

        $mensagem = new Message();
        $mensagem->forceFill([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'conteudo' => sanitizeString($request->content),
        ])->save();

        return response()->json($mensagem->load(['sender', 'receiver']), 201);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $userId = $request->user()->id;

        $mensagem = Message::findOrFail($id);

        if ($mensagem->sender_id !== $userId && $mensagem->receiver_id !== $userId) {
            return response()->json(['message' => 'Você não tem permissão para excluir esta mensagem.'], 403);
        }

        $mensagem->delete();

        return response()->json(['message' => 'Mensagem excluída com sucesso.']);
    }
}
