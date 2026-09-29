import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import type { Service } from '@/types';
import { useAuth } from '@/hooks/useAuth';
import { getMeusServicos, deleteServico, toggleServicoStatus } from '@/services/api';
import { storageUrl } from '@/services/config';
import ServiceDetailModal from '@/components/ServiceDetailModal';

export default function MeusServicos() {
    const { user } = useAuth();
    const [servicos, setServicos] = useState<Service[]>([]);
    const [carregando, setCarregando] = useState(true);
    const [excluindo, setExcluindo] = useState<number | null>(null);
    const [servicoVisualizar, setServicoVisualizar] = useState<Service | null>(null);

    useEffect(() => {
        if (!user) return;
        getMeusServicos()
            .then((res) => setServicos((res.data as Service[]) || []))
            .catch(() => {})
            .finally(() => setCarregando(false));
    }, [user]);

    const handleExcluir = async (id: number) => {
        if (!confirm('Tem certeza que deseja excluir este serviço?')) return;

        setExcluindo(id);
        try {
            await deleteServico(id);
            setServicos((prev) => prev.filter((s) => s.id !== id));
        } catch {
            // Erro ao excluir
        } finally {
            setExcluindo(null);
        }
    };

    const handleToggleStatus = async (id: number) => {
        try {
            await toggleServicoStatus(id);
            setServicos((prev) =>
                prev.map((s) =>
                    s.id === id ? { ...s, status: s.status === 'active' ? 'inactive' : 'active' } : s
                )
            );
        } catch {
            // Erro ao atualizar
        }
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
            <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Meus Serviços</h1>
                    <p className="mt-1 text-sm text-gray-500">
                        Gerencie seus serviços cadastrados. ({servicos.filter((s) => s.status === 'active').length}/5 ativos)
                    </p>
                </div>
                {user?.status === 'approved' && servicos.filter((s) => s.status === 'active').length < 5 && (
                    <Link
                        to="/app/servicos/novo"
                        className="inline-flex items-center rounded-lg bg-[#f4623a] px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-[#e5512e]"
                    >
                        <svg className="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                        </svg>
                        Novo Serviço
                    </Link>
                )}
            </div>

            {user?.status !== 'approved' && (
                <div className="rounded-xl border border-amber-200 bg-amber-50 p-5">
                    <div className="flex items-start gap-3">
                        <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-lg">⏳</div>
                        <div>
                            <h3 className="text-sm font-semibold text-amber-900">Conta ainda não aprovada</h3>
                            <p className="mt-1 text-sm text-amber-700">
                                Você não pode cadastrar serviços enquanto sua conta não for aprovada pelo administrador.
                            </p>
                        </div>
                    </div>
                </div>
            )}

            {servicos.length === 0 ? (
                <div className="rounded-xl border border-gray-200 bg-white p-12 text-center shadow-sm">
                    <div className="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 text-3xl">
                        🔧
                    </div>
                    <h2 className="mt-4 text-lg font-semibold text-gray-900">Nenhum serviço cadastrado</h2>
                    <p className="mt-2 max-w-sm mx-auto text-sm text-gray-500">
                        {user?.status === 'approved'
                            ? 'Comece cadastrando seu primeiro serviço para trocar com outros condôminos.'
                            : 'Aguarde a aprovação da sua conta para cadastrar serviços.'}
                    </p>
                    {user?.status === 'approved' && (
                        <Link
                            to="/app/servicos/novo"
                            className="mt-4 inline-flex items-center rounded-lg bg-[#f4623a] px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-[#e5512e]"
                        >
                            Cadastrar Primeiro Serviço
                        </Link>
                    )}
                </div>
            ) : (
                <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {servicos.map((servico) => (
                        <div
                            key={servico.id}
                            className="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-shadow hover:shadow-md"
                        >
                            {servico.imagem ? (
                                <img src={storageUrl(servico.imagem)} alt={servico.titulo} className="mb-3 h-40 w-full rounded-lg object-cover" />
                            ) : (
                                <div className="mb-3 flex h-40 items-center justify-center rounded-lg bg-gray-100 text-4xl">🔧</div>
                            )}
                            <div className="mb-3 flex items-center justify-between">
                                <span className="inline-block rounded-full bg-[#f4623a]/10 px-3 py-1 text-xs font-medium text-[#f4623a]">
                                    {servico.categoria || 'Geral'}
                                </span>
                                <span
                                    className={`inline-block rounded-full px-2 py-0.5 text-xs font-medium ${
                                        servico.status === 'active'
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-gray-100 text-gray-500'
                                    }`}
                                >
                                    {servico.status === 'active' ? 'Ativo' : 'Inativo'}
                                </span>
                            </div>
                            <h3 className="text-lg font-semibold text-gray-900">{servico.titulo}</h3>
                            <p className="mt-2 line-clamp-2 text-sm text-gray-500">{servico.descricao}</p>
                            <div className="mt-4 flex items-center gap-2 border-t border-gray-100 pt-3">
                                <button
                                    onClick={() => setServicoVisualizar(servico)}
                                    className="rounded-lg bg-[#f4623a] px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-[#c34e2e]"
                                >
                                    Visualizar
                                </button>
                                <Link
                                    to={`/app/servicos/editar/${servico.id}`}
                                    className="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-center text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50"
                                >
                                    Editar
                                </Link>
                                <button
                                    onClick={() => handleToggleStatus(servico.id)}
                                    className="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-center text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50"
                                >
                                    {servico.status === 'active' ? 'Desativar' : 'Ativar'}
                                </button>
                                <button
                                    onClick={() => handleExcluir(servico.id)}
                                    disabled={excluindo === servico.id}
                                    className="rounded-lg border border-red-300 px-3 py-2 text-sm font-medium text-red-600 transition-colors hover:bg-red-50 disabled:opacity-50"
                                >
                                    {excluindo === servico.id ? '...' : 'Excluir'}
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            )}

            {servicoVisualizar && (
                <ServiceDetailModal
                    servico={servicoVisualizar}
                    onClose={() => setServicoVisualizar(null)}
                    actionButton={
                        <Link
                            to={`/app/servicos/editar/${servicoVisualizar.id}`}
                            className="flex-1 rounded-lg bg-[#f4623a] px-4 py-2.5 text-center text-sm font-medium text-white transition-colors hover:bg-[#c34e2e]"
                        >
                            Editar
                        </Link>
                    }
                />
            )}
        </div>
    );
}
