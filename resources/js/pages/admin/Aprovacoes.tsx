import { useState, useEffect } from 'react';
import { useSearchParams } from 'react-router-dom';
import type { User } from '@/types';
import Avatar from '@/components/Avatar';
import { getAdminUsuarios, approveUsuario, rejectUsuario } from '@/services/api';
import { formatCpf } from '@/utils/formatCpf';

interface PaginatedUsers {
    data: User[];
    current_page: number;
    last_page: number;
    total: number;
}

export default function Aprovacoes() {
    const [searchParams, setSearchParams] = useSearchParams();
    const [usuarios, setUsuarios] = useState<PaginatedUsers | null>(null);
    const [carregando, setCarregando] = useState(true);
    const [pagina, setPagina] = useState(1);
    const [toast, setToast] = useState<{ type: 'success' | 'error'; message: string } | null>(null);

    const redirectUserId = searchParams.get('user_id');
    const redirectAction = searchParams.get('action');

    const carregarPendentes = async (page: number = 1) => {
        setCarregando(true);
        try {
            const data = await getAdminUsuarios({ status: 'pending', page: page.toString() });
            setUsuarios(data as PaginatedUsers);
        } catch {
            // Erro ao carregar
        } finally {
            setCarregando(false);
        }
    };

    useEffect(() => {
        carregarPendentes(pagina);
    }, [pagina]);

    useEffect(() => {
        if (redirectUserId && redirectAction) {
            setToast({
                type: 'success',
                message: redirectAction === 'approve'
                    ? 'Link verificado! Clique em "Aprovar" para confirmar.'
                    : 'Link verificado! Clique em "Rejeitar" para confirmar.'
            });
            setSearchParams({}, { replace: true });
        }
    }, [redirectUserId, redirectAction, setSearchParams]);

    useEffect(() => {
        if (toast) {
            const timer = setTimeout(() => setToast(null), 5000);
            return () => clearTimeout(timer);
        }
    }, [toast]);

    const handleApprove = async (id: number) => {
        try {
            await approveUsuario(id);
            setToast({ type: 'success', message: 'Usuário aprovado com sucesso!' });
            carregarPendentes(pagina);
        } catch {
            setToast({ type: 'error', message: 'Erro ao aprovar usuário.' });
        }
    };

    const handleReject = async (id: number) => {
        if (!confirm('Tem certeza que deseja rejeitar este usuário?')) return;

        try {
            await rejectUsuario(id);
            setToast({ type: 'success', message: 'Usuário rejeitado.' });
            carregarPendentes(pagina);
        } catch {
            setToast({ type: 'error', message: 'Erro ao rejeitar usuário.' });
        }
    };

    return (
        <div className="space-y-6">
            {toast && (
                <div className={`fixed top-4 right-4 z-50 rounded-lg px-4 py-3 text-sm font-medium text-white shadow-lg transition-all ${
                    toast.type === 'success' ? 'bg-green-600' : 'bg-red-600'
                }`}>
                    {toast.message}
                </div>
            )}

            <div>
                <h1 className="text-2xl font-bold text-gray-900">Aprovações Pendentes</h1>
                <p className="mt-1 text-sm text-gray-500">Aprove ou rejeite os usuários aguardando aprovação.</p>
            </div>

            {carregando ? (
                <div className="flex items-center justify-center py-12">
                    <div className="h-8 w-8 animate-spin rounded-full border-4 border-[#f4623a] border-t-transparent" />
                </div>
            ) : !usuarios || usuarios.data.length === 0 ? (
                <div className="rounded-xl border border-gray-200 bg-white p-12 text-center shadow-sm">
                    <div className="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-3xl">
                        ✅
                    </div>
                    <h2 className="mt-4 text-lg font-semibold text-gray-900">Nenhuma aprovação pendente</h2>
                    <p className="mt-2 max-w-sm mx-auto text-sm text-gray-500">
                        Todos os usuários já foram processados.
                    </p>
                </div>
            ) : (
                <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {usuarios.data.map((usuario) => (
                        <div
                            key={usuario.id}
                            className="rounded-xl border border-gray-200 bg-white p-5 shadow-sm"
                        >
                            <div className="flex items-center gap-3">
                                <Avatar name={usuario.name} avatar={usuario.profile?.avatar} size="xl" />
                                <div>
                                    <p className="font-medium text-gray-900">{usuario.name}</p>
                                    <p className="text-sm text-gray-500">{usuario.email}</p>
                                </div>
                            </div>

                            <div className="mt-4 space-y-2 text-sm">
                                <div className="flex justify-between">
                                    <span className="text-gray-500">CPF:</span>
                                    <span className="font-medium text-gray-900">{formatCpf(usuario.cpf)}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-gray-500">Cadastrado em:</span>
                                    <span className="font-medium text-gray-900">
                                        {new Date(usuario.created_at).toLocaleDateString('pt-BR')}
                                    </span>
                                </div>
                            </div>

                            <div className="mt-4 flex items-center gap-2 border-t border-gray-100 pt-3">
                                <button
                                    onClick={() => handleApprove(usuario.id)}
                                    className="flex-1 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-green-700"
                                >
                                    Aprovar
                                </button>
                                <button
                                    onClick={() => handleReject(usuario.id)}
                                    className="flex-1 rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-600 transition-colors hover:bg-red-50"
                                >
                                    Rejeitar
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            )}

            {/* Paginação */}
            {usuarios && usuarios.last_page > 1 && (
                <div className="flex items-center justify-between">
                    <p className="text-sm text-gray-500">
                        Página {usuarios.current_page} de {usuarios.last_page} ({usuarios.total} pendentes)
                    </p>
                    <div className="flex gap-2">
                        <button
                            onClick={() => setPagina((p) => Math.max(1, p - 1))}
                            disabled={usuarios.current_page <= 1}
                            className="rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50"
                        >
                            Anterior
                        </button>
                        <button
                            onClick={() => setPagina((p) => Math.min(usuarios.last_page, p + 1))}
                            disabled={usuarios.current_page >= usuarios.last_page}
                            className="rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50"
                        >
                            Próxima
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}
