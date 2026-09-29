import { useState, useEffect, useRef } from 'react';
import type { Message } from '@/types';
import Avatar from '@/components/Avatar';

interface ChatWindowProps {
    messages: Message[];
    currentUserId: number;
    otherUserId: number;
    onSend: (content: string) => void;
    otherUserName?: string;
    otherUserAvatar?: string | null;
    onBack?: () => void;
}

export default function ChatWindow({ messages, currentUserId, otherUserId, onSend, otherUserName, otherUserAvatar, onBack }: ChatWindowProps) {
    const [input, setInput] = useState('');
    const messagesEndRef = useRef<HTMLDivElement>(null);
    const inputRef = useRef<HTMLInputElement>(null);

    // Ordena mensagens por data crescente (mais antigas primeiro, mais novas embaixo)
    const sortedMessages = [...messages].sort((a, b) => new Date(a.created_at).getTime() - new Date(b.created_at).getTime());

    useEffect(() => {
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [messages]);

    useEffect(() => {
        inputRef.current?.focus();
    }, [otherUserId]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (!input.trim()) return;
        onSend(input.trim());
        setInput('');
        inputRef.current?.focus();
    };

    const formatTime = (dateStr: string) => {
        const d = new Date(dateStr);
        return d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    };

    const formatDate = (dateStr: string) => {
        const d = new Date(dateStr);
        const today = new Date();
        if (d.toDateString() === today.toDateString()) return 'Hoje';
        return d.toLocaleDateString('pt-BR', { day: '2-digit', month: 'short' });
    };

    return (
        <div className="flex flex-1 flex-col bg-white h-full">
            {/* Header do chat */}
            {otherUserName && (
                <div className="flex items-center gap-3 border-b border-gray-100 px-3 sm:px-5 py-3 bg-white shadow-sm">
                    {onBack && (
                        <button
                            onClick={onBack}
                            className="flex h-8 w-8 items-center justify-center rounded-full hover:bg-gray-100 sm:hidden"
                        >
                            <svg className="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                    )}
                    <Avatar name={otherUserName} avatar={otherUserAvatar} size="sm" />
                    <div className="min-w-0">
                        <p className="text-sm font-bold text-gray-900 truncate">{otherUserName}</p>
                        <p className="text-xs text-green-500 font-medium">Online</p>
                    </div>
                </div>
            )}

            {/* Mensagens */}
            <div className="flex-1 overflow-y-auto px-3 sm:px-5 py-4 space-y-1">
                {sortedMessages.length === 0 ? (
                    <div className="flex items-center justify-center h-full">
                        <div className="text-center">
                            <div className="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#f4623a]/10 text-2xl mb-3">💬</div>
                            <p className="text-sm font-medium text-gray-700">Nenhuma mensagem ainda</p>
                            <p className="text-xs text-gray-400 mt-1">Envie a primeira mensagem para iniciar a conversa.</p>
                        </div>
                    </div>
                ) : (
                    sortedMessages.map((msg, idx) => {
                        const isOwn = msg.sender_id === currentUserId;
                        const showDate = idx === 0 || formatDate(msg.created_at) !== formatDate(sortedMessages[idx - 1].created_at);

                        return (
                            <div key={msg.id}>
                                {showDate && (
                                    <div className="flex justify-center my-3">
                                        <span className="px-3 py-1 rounded-full bg-gray-100 text-xs font-medium text-gray-400">
                                            {formatDate(msg.created_at)}
                                        </span>
                                    </div>
                                )}
                                <div className={`flex ${isOwn ? 'justify-end' : 'justify-start'} mb-1`}>
                                    <div
                                        className={`max-w-[80%] sm:max-w-[75%] rounded-2xl px-4 py-2.5 shadow-sm ${
                                            isOwn
                                                ? 'bg-[#f4623a] text-white rounded-br-md'
                                                : 'bg-gray-100 text-gray-900 rounded-bl-md'
                                        }`}
                                    >
                                        <p className="text-sm leading-relaxed">{msg.conteudo}</p>
                                        <p className={`text-[10px] mt-1 text-right ${isOwn ? 'text-white/60' : 'text-gray-400'}`}>
                                            {formatTime(msg.created_at)}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        );
                    })
                )}
                <div ref={messagesEndRef} />
            </div>

            {/* Input */}
            <div className="border-t border-gray-100 bg-white px-3 sm:px-4 py-3">
                <form onSubmit={handleSubmit} className="flex items-end gap-2">
                    <div className="flex-1 relative">
                        <input
                            ref={inputRef}
                            type="text"
                            value={input}
                            onChange={(e) => setInput(e.target.value)}
                            onKeyDown={(e) => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); handleSubmit(e); } }}
                            placeholder="Digite sua mensagem..."
                            className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 pr-12 text-sm text-gray-900 placeholder-gray-400 transition-all focus:border-[#f4623a] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#f4623a]/20"
                        />
                        <button
                            type="submit"
                            disabled={!input.trim()}
                            className="absolute right-2 top-1/2 -translate-y-1/2 flex h-8 w-8 items-center justify-center rounded-full transition-all disabled:opacity-30"
                            style={{ color: input.trim() ? '#f4623a' : '#d1d5db' }}
                        >
                            <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>
                </form>
                <p className="text-[10px] text-gray-300 mt-1.5 text-center hidden sm:block">Pressione Enter para enviar</p>
            </div>
        </div>
    );
}
