import { useState, useEffect } from 'react';
import type { Exchange, Service, User } from '@/types';
import { useAuth } from '@/hooks/useAuth';
import Avatar from '@/components/Avatar';
import { getExchanges, confirmExchange, cancelExchange } from '@/services/api';

interface ExchangeWithDetails extends Exchange {
    service_proponente?: Service;
    service_receptor?: Service;
    user_proponente?: User;
    user_receptor?: User;
}

export default function Trocas() {
    const { user } = useAuth();
    const [trocas, setTrocas] = useState<ExchangeWithDetails[]>([]);
    const [carregando, setCarregando] = useState(true);
    const [filtro, setFiltro] = useState('all');

    useEffect(() => {
        getExchanges()
            .then((res) => setTrocas((res.data as ExchangeWithDetails[]) || []))
            .catch(() => {})
            .finally(() => setCarregando(false));
    }, []);

    const handleConfirm = async (id: number) => {
        try {
            await confirmExchange(id);
            setTrocas((prev) =>
                prev.map((t) => (t.id === id ? { ...t, status: 'confirmed' } : t))
            );
        } catch {
            // Erro ao confirmar
        }
    };

    const handleCancel = async (id: number) => {
        if (!confirm('Tem certeza que deseja cancelar esta troca?')) return;

        try {
            await cancelExchange(id);
            setTrocas((prev) =>
                prev.map((t) => (t.id === id ? { ...t, status: 'cancelled' } : t))
            );
        } catch {
            // Erro ao cancelar
        }
    };

    const trocasFiltradas = filtro === 'all'
        ? trocas
        : trocas.filter((t) => t.status === filtro);

    const statusColors: Record<string, string> = {
        pending: 'bg-yellow-100 text-yellow-700',
        confirmed: 'bg-blue-100 text-blue-700',
        completed: 'bg-green-100 text-green-700',
        cancelled: 'bg-gray-100 text-gray-500',
    };

    const statusLabels: Record<string, string> = {
        pending: 'Pendente',
        confirmed: 'Confirmada',
        completed: 'Concluída',
        cancelled: 'Cancelada',
    };

    if (carregando) {
        return (
            <div className="flex items-center justify-center py-12">
                <div className="h-8 w-8 animate-spin rounded-full border-4 border-[#f4623a] border-t-transparent" />
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div>
                <h1 className="text-2xl font-bold text-gray-900">Trocas</h1>
                <p className="mt-1 text-sm text-gray-500">Gerencie suas trocas de serviços com outros condôminos.</p>
            </div>

            {/* Filtros */}
            <div className="flex flex-wrap gap-2">
                {[
                    { value: 'all', label: 'Todas' },
                    { value: 'pending', label: 'Pendentes' },
                    { value: 'confirmed', label: 'Confirmadas' },
                    { value: 'completed', label: 'Concluídas' },
                    { value: 'cancelled', label: 'Canceladas' },
                ].map((f) => (
                    <button
                        key={f.value}
                        onClick={() => setFiltro(f.value)}
                        className={`rounded-lg px-4 py-2 text-sm font-medium transition-colors ${
                            filtro === f.value
                                ? 'bg-[#f4623a] text-white'
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                        }`}
                    >
                        {f.label}
                    </button>
                ))}
            </div>

            {trocasFiltradas.length === 0 ? (
                <div className="rounded-xl border border-gray-200 bg-white p-12 text-center shadow-sm">
                    <div className="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 text-3xl">
                        🔄
                    </div>
                    <h2 className="mt-4 text-lg font-semibold text-gray-900">Nenhuma troca encontrada</h2>
                    <p className="mt-2 max-w-sm mx-auto text-sm text-gray-500">
                        {filtro === 'all'
                            ? 'Você ainda não participa de nenhuma troca.'
                            : 'Nenhuma troca com este status.'}
                    </p>
                </div>
            ) : (
                <div className="space-y-4">
                    {trocasFiltradas.map((troca) => {
                        const isProponente = user?.id === troca.user_proponente_id;
                        const outroUsuario = isProponente ? troca.user_receptor : troca.user_proponente;
                        const meuServico = isProponente ? troca.service_proponente : troca.service_receptor;
                        const outroServico = isProponente ? troca.service_receptor : troca.service_proponente;

                        return (
                            <div
                                key={troca.id}
                                className="rounded-xl border border-gray-200 bg-white p-5 shadow-sm"
                            >
                                <div className="flex items-start justify-between">
                                    <div className="flex items-center gap-3">
                                                <Avatar name={outroUsuario?.name} avatar={outroUsuario?.profile?.avatar} size="lg" />
                                        <div>
                                            <p className="text-sm font-medium text-gray-900">
                                                Troca com {outroUsuario?.name}
                                            </p>
                                            <p className="text-xs text-gray-500">
                                                {isProponente ? 'Você propôs' : 'Proposta recebida'}
                                            </p>
                                        </div>
                                    </div>
                                    <span className={`rounded-full px-3 py-1 text-xs font-medium ${statusColors[troca.status]}`}>
                                        {statusLabels[troca.status]}
                                    </span>
                                </div>

                                <div className="mt-4 grid grid-cols-2 gap-4">
                                    <div className="rounded-lg bg-gray-50 p-3">
                                        <p className="text-xs font-medium text-gray-500">Seu serviço</p>
                                        <p className="text-sm font-medium text-gray-900">{meuServico?.titulo}</p>
                                    </div>
                                    <div className="rounded-lg bg-gray-50 p-3">
                                        <p className="text-xs font-medium text-gray-500">Serviço oferecido</p>
                                        <p className="text-sm font-medium text-gray-900">{outroServico?.titulo}</p>
                                    </div>
                                </div>

                                {troca.status === 'pending' && (
                                    <div className="mt-4 flex items-center gap-2 border-t border-gray-100 pt-3">
                                        {!isProponente && (
                                            <button
                                                onClick={() => handleConfirm(troca.id)}
                                                className="rounded-lg bg-[#f4623a] px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-[#e5512e]"
                                            >
                                                Confirmar
                                            </button>
                                        )}
                                        <button
                                            onClick={() => handleCancel(troca.id)}
                                            className="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50"
                                        >
                                            Cancelar
                                        </button>
                                    </div>
                                )}
                            </div>
                        );
                    })}
                </div>
            )}
        </div>
    );
}
