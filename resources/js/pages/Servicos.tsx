import { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import type { Service } from '@/types';
import { useAuth } from '@/hooks/useAuth';
import { getServicos, deleteServico } from '@/services/api';
import { storageUrl } from '@/services/config';
import Avatar from '@/components/Avatar';
import ServiceDetailModal from '@/components/ServiceDetailModal';

const CATEGORIAS = [
    'Encanador', 'Eletricista', 'Pintor', 'Marceneiro', 'Jardinagem',
    'Limpeza', 'Manutenção', 'Tecnologia', 'Aulas', 'Outros',
];

const CATEGORIA_ICONS: Record<string, string> = {
    'Encanador': '🔧', 'Eletricista': '⚡', 'Pintor': '🎨',
    'Marceneiro': '🪚', 'Jardinagem': '🌿', 'Limpeza': '🧹',
    'Manutenção': '🛠️', 'Tecnologia': '💻', 'Aulas': '📚', 'Outros': '📋',
};

export default function Servicos() {
    const { user } = useAuth();
    const navigate = useNavigate();
    const [servicos, setServicos] = useState<Service[]>([]);
    const [busca, setBusca] = useState('');
    const [filtroCategoria, setFiltroCategoria] = useState('');
    const [carregando, setCarregando] = useState(true);
    const [excluindo, setExcluindo] = useState<number | null>(null);
    const [servicoVisualizar, setServicoVisualizar] = useState<Service | null>(null);

    useEffect(() => {
        getServicos()
            .then((res) => setServicos(res.data as Service[]))
            .catch(() => {})
            .finally(() => setCarregando(false));
    }, []);

    const servicosFiltrados = servicos.filter((s) => {
        const matchBusca = !busca ||
            s.titulo?.toLowerCase().includes(busca.toLowerCase()) ||
            s.descricao?.toLowerCase().includes(busca.toLowerCase()) ||
            s.categoria?.toLowerCase().includes(busca.toLowerCase());
        const matchCategoria = !filtroCategoria || s.categoria === filtroCategoria;
        return matchBusca && matchCategoria;
    });

    const handleExcluir = async (id: number) => {
        if (!confirm('Tem certeza que deseja excluir este serviço?')) return;
        setExcluindo(id);
        try {
            await deleteServico(id);
            setServicos((prev) => prev.filter((s) => s.id !== id));
        } catch { /* erro */ } finally {
            setExcluindo(null);
        }
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Serviços</h1>
                    <p className="mt-1 text-sm text-gray-500">Explore os serviços disponíveis na plataforma.</p>
                </div>
                {user && (
                    <Link
                        to="/app/servicos/meus"
                        className="inline-flex items-center gap-2 rounded-lg bg-[#f4623a] px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-[#c34e2e]"
                    >
                        <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16m-7 6h7" />
                        </svg>
                        Meus Serviços
                    </Link>
                )}
            </div>

            {/* Barra de busca e filtros */}
            <div className="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div className="flex flex-col gap-3 sm:flex-row">
                    <div className="relative flex-1">
                        <input
                            type="text"
                            placeholder="Buscar por título, descrição ou categoria..."
                            value={busca}
                            onChange={(e) => setBusca(e.target.value)}
                            className="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 pl-11 text-sm text-gray-900 placeholder-gray-400 transition-colors focus:border-[#f4623a] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#f4623a]/20"
                        />
                        <svg className="absolute left-3.5 top-3.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <select
                        value={filtroCategoria}
                        onChange={(e) => setFiltroCategoria(e.target.value)}
                        className="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700 focus:border-[#f4623a] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#f4623a]/20"
                    >
                        <option value="">Todas as categorias</option>
                        {CATEGORIAS.map((cat) => (
                            <option key={cat} value={cat}>{CATEGORIA_ICONS[cat]} {cat}</option>
                        ))}
                    </select>
                </div>
                {(busca || filtroCategoria) && (
                    <div className="mt-3 flex items-center gap-2">
                        <span className="text-xs text-gray-400">{servicosFiltrados.length} resultado(s)</span>
                        <button
                            onClick={() => { setBusca(''); setFiltroCategoria(''); }}
                            className="text-xs text-[#f4623a] hover:underline"
                        >
                            Limpar filtros
                        </button>
                    </div>
                )}
            </div>

            {/* Lista */}
            {carregando ? (
                <div className="flex items-center justify-center py-12">
                    <div className="h-8 w-8 animate-spin rounded-full border-4 border-[#f4623a] border-t-transparent" />
                </div>
            ) : servicosFiltrados.length === 0 ? (
                <div className="rounded-xl border border-gray-200 bg-white p-12 text-center shadow-sm">
                    <div className="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 text-3xl mb-4">🔧</div>
                    <p className="text-lg font-medium text-gray-900">Nenhum serviço encontrado</p>
                    <p className="mt-1 text-sm text-gray-500">
                        {busca || filtroCategoria ? 'Tente buscar com outros termos.' : 'Ainda não há serviços cadastrados.'}
                    </p>
                </div>
            ) : (
                <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {servicosFiltrados.map((servico) => {
                        const isOwner = user?.id === servico.user_id;
                        return (
                            <div key={servico.id} className="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-shadow hover:shadow-md flex flex-col">
                                {servico.imagem ? (
                                    <img src={storageUrl(servico.imagem)} alt={servico.titulo} className="mb-3 h-40 w-full rounded-lg object-cover" />
                                ) : (
                                    <div className="mb-3 flex h-40 items-center justify-center rounded-lg bg-gray-100 text-4xl">
                                        {CATEGORIA_ICONS[servico.categoria] || '📋'}
                                    </div>
                                )}
                                <div className="mb-3 flex items-center justify-between">
                                    <span className="inline-flex items-center gap-1 rounded-full bg-[#f4623a]/10 px-3 py-1 text-xs font-medium text-[#f4623a]">
                                        {CATEGORIA_ICONS[servico.categoria] || '📋'} {servico.categoria || 'Geral'}
                                    </span>
                                    <span className={`inline-block rounded-full px-2 py-0.5 text-xs font-medium ${
                                        servico.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'
                                    }`}>
                                        {servico.status === 'active' ? 'Ativo' : 'Inativo'}
                                    </span>
                                </div>

                                <h3 className="text-lg font-semibold text-gray-900 mb-1">{servico.titulo}</h3>
                                <p className="text-sm text-gray-500 line-clamp-2 mb-3 flex-1">{servico.descricao}</p>

                                {servico.valor_sugerido && (
                                    <p className="text-sm font-semibold text-[#f4623a] mb-3">
                                        R$ {Number(servico.valor_sugerido).toFixed(2)}
                                    </p>
                                )}

                                <div className="flex items-center gap-2 border-t border-gray-100 pt-3 mt-auto">
                                    {servico.user && (
                                        <div className="flex items-center gap-2 flex-1 min-w-0">
                                            <Avatar name={servico.user.name} avatar={servico.user.profile?.avatar} size="xs" />
                                            <span className="text-sm text-gray-600 truncate">{servico.user.name}</span>
                                        </div>
                                    )}
                                    <div className="flex items-center gap-1.5 shrink-0">
                                        <button
                                            onClick={() => setServicoVisualizar(servico)}
                                            className="rounded-lg bg-[#f4623a] px-2.5 py-1.5 text-xs font-medium text-white transition-colors hover:bg-[#c34e2e]"
                                        >
                                            Visualizar
                                        </button>
                                        {isOwner && (
                                            <>
                                                <Link
                                                    to={`/app/servicos/editar/${servico.id}`}
                                                    className="rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs font-medium text-gray-600 transition-colors hover:bg-gray-50"
                                                >
                                                    Editar
                                                </Link>
                                                <button
                                                    onClick={() => handleExcluir(servico.id)}
                                                    disabled={excluindo === servico.id}
                                                    className="rounded-lg border border-red-200 px-2.5 py-1.5 text-xs font-medium text-red-600 transition-colors hover:bg-red-50 disabled:opacity-50"
                                                >
                                                    {excluindo === servico.id ? '...' : 'Excluir'}
                                                </button>
                                            </>
                                        )}
                                        <button
                                            onClick={() => {
                                                if (!user) { navigate('/entrar'); return; }
                                                if (user.status !== 'approved') { navigate('/app/mensagens?not_approved=1'); return; }
                                                navigate(`/app/mensagens?user=${servico.user_id}`);
                                            }}
                                            className="rounded-lg bg-[#f4623a] px-2.5 py-1.5 text-xs font-medium text-white transition-colors hover:bg-[#c34e2e]"
                                        >
                                            Mensagem
                                        </button>
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                </div>
            )}

            {servicoVisualizar && (
                <ServiceDetailModal
                    servico={servicoVisualizar}
                    onClose={() => setServicoVisualizar(null)}
                    showProvider
                    actionButton={
                        <button
                            onClick={() => {
                                setServicoVisualizar(null);
                                if (!user) { navigate('/entrar'); return; }
                                if (user.status !== 'approved') { navigate('/app/mensagens?not_approved=1'); return; }
                                navigate(`/app/mensagens?user=${servicoVisualizar.user_id}`);
                            }}
                            className="flex-1 rounded-lg bg-[#f4623a] px-4 py-2.5 text-center text-sm font-medium text-white transition-colors hover:bg-[#c34e2e]"
                        >
                            Enviar Mensagem
                        </button>
                    }
                />
            )}
        </div>
    );
}
