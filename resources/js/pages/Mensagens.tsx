import { useState, useEffect, useCallback } from 'react';
import { useSearchParams } from 'react-router-dom';
import type { User, Message } from '@/types';
import { useAuth } from '@/hooks/useAuth';
import { getConversas, getMensagens, enviarMensagem } from '@/services/api';
import { usePolling } from '@/hooks/usePolling';
import ChatWindow from '@/components/ChatWindow';
import Avatar from '@/components/Avatar';

interface Conversa {
    user: User;
    last_message: Message;
    unread_count: number;
}

export default function Mensagens() {
    const { user } = useAuth();
    const [searchParams] = useSearchParams();
    const [conversas, setConversas] = useState<Conversa[]>([]);
    const [selectedUserId, setSelectedUserId] = useState<number | null>(null);
    const [mensagens, setMensagens] = useState<Message[]>([]);
    const [loading, setLoading] = useState(true);

    const carregarConversas = useCallback(async () => {
        try {
            const res = await getConversas();
            setConversas(res.data as Conversa[]);
        } catch { /* polling */ }
    }, []);

    const carregarMensagens = useCallback(async (userId: number) => {
        try {
            const res = await getMensagens(userId);
            setMensagens(res.data as Message[]);
        } catch { /* polling */ }
    }, []);

    useEffect(() => {
        setLoading(true);
        carregarConversas().then(() => {
            const userIdParam = searchParams.get('user');
            if (userIdParam) {
                const targetId = parseInt(userIdParam, 10);
                if (!isNaN(targetId)) setSelectedUserId(targetId);
            }
        }).finally(() => setLoading(false));
    }, [carregarConversas, searchParams]);

    usePolling(() => {
        carregarConversas();
        if (selectedUserId) carregarMensagens(selectedUserId);
    }, 3000);

    useEffect(() => {
        if (selectedUserId) carregarMensagens(selectedUserId);
    }, [selectedUserId, carregarMensagens]);

    const [sendError, setSendError] = useState<string | null>(null);

    const handleSendMessage = async (content: string) => {
        if (!selectedUserId || !content.trim()) return;
        if (user?.status !== 'approved') return;
        setSendError(null);
        try {
            const res = await enviarMensagem(selectedUserId, content);
            setMensagens((prev) => [...prev, res.data as Message]);
        } catch (err: any) {
            setSendError(err?.data?.message || err?.message || 'Erro ao enviar mensagem. Tente novamente.');
        }
    };

    const notApproved = searchParams.get('not_approved') === '1';
    const selectedConversa = conversas.find((c) => c.user.id === selectedUserId);

    if (loading) {
        return (
            <div className="flex items-center justify-center py-12">
                <div className="h-8 w-8 animate-spin rounded-full border-4 border-[#f4623a] border-t-transparent" />
            </div>
        );
    }

    return (
        <div className="space-y-4 sm:space-y-6">
            <div>
                <h1 className="text-xl sm:text-2xl font-bold text-gray-900">Mensagens</h1>
                <p className="mt-1 text-xs sm:text-sm text-gray-500">Converse com outros condôminos da plataforma.</p>
            </div>

            {notApproved && (
                <div className="rounded-xl border border-amber-200 bg-amber-50 p-4 sm:p-5">
                    <div className="flex items-start gap-3">
                        <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-lg">⏳</div>
                        <div>
                            <h3 className="text-sm font-semibold text-amber-900">Conta ainda não aprovada</h3>
                            <p className="mt-1 text-xs sm:text-sm text-amber-700">
                                Sua conta ainda não foi aprovada pelo administrador. Enquanto isso, você não poderá enviar mensagens.
                            </p>
                        </div>
                    </div>
                </div>
            )}

            <div className="flex h-[calc(100vh-200px)] sm:h-[calc(100vh-180px)] rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                {/* Sidebar de conversas - oculta no mobile quando chat aberto */}
                <div className={`${selectedUserId ? 'hidden sm:flex' : 'flex'} w-full sm:w-72 border-r border-gray-200 flex-col bg-gray-50/30`}>
                    <div className="p-3 sm:p-4 border-b border-gray-200">
                        <h2 className="text-sm font-semibold text-gray-900">Conversas</h2>
                        <p className="text-xs text-gray-400 mt-0.5">{conversas.length} conversa(s)</p>
                    </div>

                    <div className="flex-1 overflow-y-auto">
                        {conversas.length === 0 ? (
                            <div className="p-6 text-center">
                                <p className="text-sm text-gray-500">Nenhuma conversa ainda.</p>
                                <p className="mt-1 text-xs text-gray-400">Inicie uma conversa no perfil de um usuário.</p>
                            </div>
                        ) : (
                            <div className="divide-y divide-gray-100">
                                {conversas.map((conversa) => (
                                    <button
                                        key={conversa.user.id}
                                        onClick={() => setSelectedUserId(conversa.user.id)}
                                        className={`w-full p-3 sm:p-4 text-left transition-all ${
                                            selectedUserId === conversa.user.id
                                                ? 'bg-[#f4623a]/5 border-l-2 border-[#f4623a]'
                                                : 'hover:bg-gray-50 border-l-2 border-transparent'
                                        }`}
                                    >
                                        <div className="flex items-center gap-3">
                                            <Avatar name={conversa.user.name} avatar={conversa.user.profile?.avatar} size="lg" />
                                            <div className="flex-1 min-w-0">
                                                <div className="flex items-center justify-between">
                                                    <p className={`text-sm truncate ${
                                                        selectedUserId === conversa.user.id
                                                            ? 'font-bold text-[#f4623a]'
                                                            : 'font-medium text-gray-900'
                                                    }`}>
                                                        {conversa.user.name}
                                                    </p>
                                                    {conversa.unread_count > 0 && (
                                                        <span className="flex h-5 w-5 items-center justify-center rounded-full bg-[#f4623a] text-xs font-bold text-white">
                                                            {conversa.unread_count}
                                                        </span>
                                                    )}
                                                </div>
                                                <p className="text-xs text-gray-400 truncate mt-0.5">
                                                    {conversa.last_message?.conteudo || 'Nenhuma mensagem'}
                                                </p>
                                            </div>
                                        </div>
                                    </button>
                                ))}
                            </div>
                        )}
                    </div>
                </div>

                {/* Janela de chat - oculta no mobile quando sidebar aberta */}
                <div className={`${selectedUserId ? 'flex' : 'hidden sm:flex'} flex-1 flex-col min-w-0`}>
                    {selectedUserId ? (
                        <ChatWindow
                            messages={mensagens}
                            currentUserId={user?.id ?? 0}
                            otherUserId={selectedUserId}
                            otherUserName={selectedConversa?.user.name}
                            otherUserAvatar={selectedConversa?.user.profile?.avatar}
                            onSend={handleSendMessage}
                            onBack={() => setSelectedUserId(null)}
                        />
                    ) : (
                        <div className="flex-1 flex items-center justify-center bg-gray-50/30">
                            <div className="text-center px-4">
                                <div className="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-gray-100 text-4xl mb-4">💬</div>
                                <h2 className="text-lg font-semibold text-gray-900">Selecione uma conversa</h2>
                                <p className="mt-2 max-w-sm mx-auto text-sm text-gray-500">
                                    Escolha uma conversa ao lado ou inicie uma nova a partir da página de usuários.
                                </p>
                            </div>
                        </div>
                    )}
                </div>
            </div>

            {sendError && (
                <div className="rounded-xl border border-red-200 bg-red-50 p-3 sm:p-4 text-xs sm:text-sm text-red-700 flex items-center justify-between">
                    <span>{sendError}</span>
                    <button onClick={() => setSendError(null)} className="text-red-400 hover:text-red-600 ml-3">✕</button>
                </div>
            )}
        </div>
    );
}
