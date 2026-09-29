import { useState, useEffect, useRef, Fragment } from 'react';
import type { User } from '@/types';
import Avatar from '@/components/Avatar';
import {
    getAdminUsuarios,
    approveUsuario,
    rejectUsuario,
    toggleAdminUsuario,
    deleteUsuario,
    restoreUsuario,
    forceDeleteUsuario,
} from '@/services/api';
import { formatCpf } from '@/utils/formatCpf';

interface PaginatedUsers {
    data: User[];
    current_page: number;
    last_page: number;
    total: number;
}

export default function GerenciarUsuarios() {
    const [usuarios, setUsuarios] = useState<PaginatedUsers | null>(null);
    const [carregando, setCarregando] = useState(true);
    const [busca, setBusca] = useState('');
    const [buscaDebounced, setBuscaDebounced] = useState('');
    const [filtroStatus, setFiltroStatus] = useState('');
    const [pagina, setPagina] = useState(1);
    const [expandido, setExpandido] = useState<number | null>(null);
    const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    const carregarUsuarios = async (page: number = 1, buscaAtual?: string) => {
        setCarregando(true);
        try {
            const params: Record<string, string> = { page: page.toString() };
            const termoBusca = buscaAtual !== undefined ? buscaAtual : buscaDebounced;
            if (termoBusca) params.busca = termoBusca;
            if (filtroStatus) params.status = filtroStatus;
            if (filtroStatus === 'deleted') params.deleted = '1';

            const data = await getAdminUsuarios(params);
            setUsuarios(data as PaginatedUsers);
        } catch { /* Erro ao carregar */ } finally {
            setCarregando(false);
        }
    };

    useEffect(() => {
        carregarUsuarios(pagina);
    }, [pagina, filtroStatus, buscaDebounced]);

    const handleBuscaChange = (valor: string) => {
        setBusca(valor);
        if (debounceRef.current) clearTimeout(debounceRef.current);
        debounceRef.current = setTimeout(() => {
            setBuscaDebounced(valor);
            setPagina(1);
        }, 300);
    };

    const toggleExpandir = (id: number) => {
        setExpandido(expandido === id ? null : id);
    };

    const handleApprove = async (id: number) => {
        try {
            await approveUsuario(id);
            carregarUsuarios(pagina);
        } catch { /* Erro */ }
    };

    const handleReject = async (id: number) => {
        if (!confirm('Tem certeza que deseja rejeitar este usuário?')) return;
        try {
            await rejectUsuario(id);
            carregarUsuarios(pagina);
        } catch { /* Erro */ }
    };

    const handleRestore = async (id: number) => {
        if (!confirm('Tem certeza que deseja restaurar este usuário?')) return;
        try {
            await restoreUsuario(id);
            carregarUsuarios(pagina);
        } catch { /* Erro */ }
    };

    const handleToggleAdmin = async (id: number) => {
        try {
            await toggleAdminUsuario(id);
            carregarUsuarios(pagina);
        } catch { /* Erro */ }
    };

    const handleDelete = async (id: number) => {
        if (!confirm('Tem certeza que deseja excluir este usuário?')) return;
        try {
            await deleteUsuario(id);
            carregarUsuarios(pagina);
        } catch { /* Erro */ }
    };

    const handleForceDelete = async (id: number, nome: string) => {
        if (!confirm(`ATENÇÃO: Excluir permanentemente "${nome}"? Esta ação não pode ser desfeita. O email e CPF serão removidos.`)) return;
        try {
            await forceDeleteUsuario(id);
            carregarUsuarios(pagina);
        } catch { /* Erro */ }
    };

    const statusColors: Record<string, string> = {
        pending: 'bg-yellow-100 text-yellow-700',
        approved: 'bg-green-100 text-green-700',
        rejected: 'bg-red-100 text-red-700',
    };

    const statusLabels: Record<string, string> = {
        pending: 'Pendente',
        approved: 'Aprovado',
        rejected: 'Rejeitado',
    };

    const isExcluidos = filtroStatus === 'deleted';

    return (
        <div className="space-y-6">
            <div>
                <h1 className="text-2xl font-bold text-gray-900">Gerenciar Usuários</h1>
                <p className="mt-1 text-sm text-gray-500">Visualize e gerencie os usuários da plataforma.</p>
            </div>

            {/* Filtros */}
            <div className="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div className="relative flex-1">
                    <input
                        type="text"
                        placeholder="Buscar por nome, email ou CPF..."
                        value={busca}
                        onChange={(e) => handleBuscaChange(e.target.value)}
                        className="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 pl-10 text-sm text-gray-900 placeholder-gray-400 focus:border-[#f4623a] focus:outline-none focus:ring-1 focus:ring-[#f4623a]"
                    />
                    <svg className="absolute left-3 top-3 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <select
                    value={filtroStatus}
                    onChange={(e) => { setFiltroStatus(e.target.value); setPagina(1); }}
                    className="rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 focus:border-[#f4623a] focus:outline-none focus:ring-1 focus:ring-[#f4623a]"
                >
                    <option value="">Todos os status</option>
                    <option value="pending">Pendentes</option>
                    <option value="approved">Aprovados</option>
                    <option value="rejected">Rejeitados</option>
                    <option value="deleted">Excluídos</option>
                </select>
            </div>

            {/* Tabela */}
            {carregando ? (
                <div className="flex items-center justify-center py-12">
                    <div className="h-8 w-8 animate-spin rounded-full border-4 border-[#f4623a] border-t-transparent" />
                </div>
            ) : !usuarios || usuarios.data.length === 0 ? (
                <div className="rounded-xl border border-gray-200 bg-white p-12 text-center shadow-sm">
                    <p className="text-sm text-gray-500">
                        {isExcluidos ? 'Nenhum usuário excluído encontrado.' : 'Nenhum usuário encontrado.'}
                    </p>
                </div>
            ) : (
                <div className="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead className="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th className="w-8 px-4 py-3"></th>
                                    <th className="px-4 py-3 text-left font-medium text-gray-500">Usuário</th>
                                    <th className="px-4 py-3 text-left font-medium text-gray-500">CPF</th>
                                    <th className="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                                    <th className="px-4 py-3 text-left font-medium text-gray-500">Admin</th>
                                    <th className="px-4 py-3 text-left font-medium text-gray-500">
                                        {isExcluidos ? 'Excluído em' : 'Criado em'}
                                    </th>
                                    <th className="px-4 py-3 text-right font-medium text-gray-500">Ações</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-100">
                                {usuarios.data.map((usuario) => {
                                    const isDeleted = isExcluidos || !!usuario.deleted_at;
                                    const isExpanded = expandido === usuario.id;
                                    return (
                                        <Fragment key={usuario.id}>
                                            <tr className={`hover:bg-gray-50 ${isDeleted ? 'bg-red-50/30' : ''} ${isExpanded ? 'bg-orange-50/50' : ''}`}>
                                                <td className="px-4 py-3">
                                                    <button
                                                        onClick={() => toggleExpandir(usuario.id)}
                                                        className={`flex h-8 w-8 items-center justify-center rounded-lg border transition-all ${
                                                            isExpanded
                                                                ? 'border-[#f4623a] bg-[#f4623a] text-white shadow-md'
                                                                : 'border-gray-200 bg-white text-gray-400 hover:border-[#f4623a] hover:text-[#f4623a]'
                                                        }`}
                                                        title={isExpanded ? 'Recolher detalhes' : 'Ver detalhes completos'}
                                                    >
                                                        <svg className={`h-4 w-4 transition-transform duration-200 ${isExpanded ? 'rotate-90' : ''}`} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M9 5l7 7-7 7" />
                                                        </svg>
                                                    </button>
                                                </td>
                                                <td className="px-4 py-3">
                                                    <div className="flex items-center gap-3">
                                                        <Avatar name={usuario.name} avatar={usuario.profile?.avatar} size="sm" />
                                                        <div>
                                                            <p className={`font-medium ${isDeleted ? 'text-gray-400 line-through' : 'text-gray-900'}`}>
                                                                {usuario.name}
                                                            </p>
                                                            <p className="text-xs text-gray-500">{usuario.email}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-4 py-3 text-gray-600">{formatCpf(usuario.cpf)}</td>
                                                <td className="px-4 py-3">
                                                    {isDeleted ? (
                                                        <span className="rounded-full px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-500">
                                                            Excluído
                                                        </span>
                                                    ) : (
                                                        <span className={`rounded-full px-2 py-0.5 text-xs font-medium ${statusColors[usuario.status] || ''}`}>
                                                            {statusLabels[usuario.status] || usuario.status}
                                                        </span>
                                                    )}
                                                </td>
                                                <td className="px-4 py-3 text-gray-600">
                                                    {usuario.is_admin ? 'Sim' : 'Não'}
                                                </td>
                                                <td className="px-4 py-3 text-gray-500 text-xs">
                                                    {isDeleted && usuario.deleted_at
                                                        ? new Date(usuario.deleted_at).toLocaleDateString('pt-BR')
                                                        : new Date(usuario.created_at).toLocaleDateString('pt-BR')
                                                    }
                                                </td>
                                                <td className="px-4 py-3 text-right">
                                                    <div className="flex items-center justify-end gap-1">
                                                        {isDeleted ? (
                                                            <>
                                                                <button
                                                                    onClick={() => handleRestore(usuario.id)}
                                                                    className="rounded px-2 py-1 text-xs font-medium text-green-600 hover:bg-green-50"
                                                                >
                                                                    Restaurar
                                                                </button>
                                                                <button
                                                                    onClick={() => handleForceDelete(usuario.id, usuario.name)}
                                                                    className="rounded px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50"
                                                                >
                                                                    Excluir Permanente
                                                                </button>
                                                            </>
                                                        ) : (
                                                            <>
                                                                {usuario.status === 'rejected' && (
                                                                    <button
                                                                        onClick={() => handleRestore(usuario.id)}
                                                                        className="rounded px-2 py-1 text-xs font-medium text-green-600 hover:bg-green-50"
                                                                    >
                                                                        Reativar
                                                                    </button>
                                                                )}
                                                                {usuario.status === 'pending' && (
                                                                    <>
                                                                        <button
                                                                            onClick={() => handleApprove(usuario.id)}
                                                                            className="rounded px-2 py-1 text-xs font-medium text-green-600 hover:bg-green-50"
                                                                        >
                                                                            Aprovar
                                                                        </button>
                                                                        <button
                                                                            onClick={() => handleReject(usuario.id)}
                                                                            className="rounded px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50"
                                                                        >
                                                                            Rejeitar
                                                                        </button>
                                                                    </>
                                                                )}
                                                                <button
                                                                    onClick={() => handleToggleAdmin(usuario.id)}
                                                                    className="rounded px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50"
                                                                >
                                                                    {usuario.is_admin ? 'Remover Admin' : 'Tornar Admin'}
                                                                </button>
                                                                <button
                                                                    onClick={() => handleDelete(usuario.id)}
                                                                    className="rounded px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50"
                                                                >
                                                                    Excluir
                                                                </button>
                                                            </>
                                                        )}
                                                    </div>
                                                </td>
                                            </tr>
                                            {isExpanded && (
                                                <tr>
                                                    <td colSpan={7} className="border-l-4 border-[#f4623a] bg-gradient-to-r from-orange-50 to-white px-6 py-5">
                                                        <div className="mb-3 flex items-center gap-2">
                                                            <svg className="h-5 w-5 text-[#f4623a]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                            </svg>
                                                            <span className="text-sm font-semibold text-gray-700">Detalhes do Usuário</span>
                                                        </div>
                                                        <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                                                            <div className="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                                                                <div className="mb-1 flex items-center gap-1.5">
                                                                    <svg className="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                                    </svg>
                                                                    <p className="text-xs font-medium text-gray-500">Email</p>
                                                                </div>
                                                                <p className="text-sm font-medium text-gray-900 break-all">{usuario.email || '—'}</p>
                                                            </div>
                                                            <div className="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                                                                <div className="mb-1 flex items-center gap-1.5">
                                                                    <svg className="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                                                    </svg>
                                                                    <p className="text-xs font-medium text-gray-500">CPF</p>
                                                                </div>
                                                                <p className="text-sm font-medium text-gray-900">{formatCpf(usuario.cpf) || '—'}</p>
                                                            </div>
                                                            <div className="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                                                                <div className="mb-1 flex items-center gap-1.5">
                                                                    <svg className="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                    <p className="text-xs font-medium text-gray-500">Verificado em</p>
                                                                </div>
                                                                <p className="text-sm font-medium text-gray-900">
                                                                    {usuario.email_verified_at
                                                                        ? new Date(usuario.email_verified_at).toLocaleString('pt-BR')
                                                                        : <span className="text-yellow-600">Não verificado</span>}
                                                                </p>
                                                            </div>
                                                            <div className="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                                                                <div className="mb-1 flex items-center gap-1.5">
                                                                    <svg className="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                                    </svg>
                                                                    <p className="text-xs font-medium text-gray-500">MFA Habilitado</p>
                                                                </div>
                                                                <p className="text-sm font-medium text-gray-900">
                                                                    {usuario.mfa_enabled
                                                                        ? <span className="text-green-600">Sim</span>
                                                                        : <span className="text-gray-400">Não</span>}
                                                                </p>
                                                            </div>
                                                            <div className="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                                                                <div className="mb-1 flex items-center gap-1.5">
                                                                    <svg className="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                    </svg>
                                                                    <p className="text-xs font-medium text-gray-500">Aceitou Termos</p>
                                                                </div>
                                                                <p className="text-sm font-medium text-gray-900">
                                                                    {usuario.accepted_terms_at
                                                                        ? new Date(usuario.accepted_terms_at).toLocaleString('pt-BR')
                                                                        : <span className="text-red-500">Não aceitou</span>}
                                                                </p>
                                                            </div>
                                                            <div className="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                                                                <div className="mb-1 flex items-center gap-1.5">
                                                                    <svg className="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                                    </svg>
                                                                    <p className="text-xs font-medium text-gray-500">Versão Termos</p>
                                                                </div>
                                                                <p className="text-sm font-medium text-gray-900">{usuario.terms_version || '—'}</p>
                                                            </div>
                                                            <div className="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                                                                <div className="mb-1 flex items-center gap-1.5">
                                                                    <svg className="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                    <p className="text-xs font-medium text-gray-500">Último Acesso</p>
                                                                </div>
                                                                <p className="text-sm font-medium text-gray-900">
                                                                    {usuario.last_login_at
                                                                        ? new Date(usuario.last_login_at).toLocaleString('pt-BR')
                                                                        : <span className="text-gray-400">Nunca</span>}
                                                                </p>
                                                            </div>
                                                            <div className="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                                                                <div className="mb-1 flex items-center gap-1.5">
                                                                    <svg className="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                                                    </svg>
                                                                    <p className="text-xs font-medium text-gray-500">Último IP</p>
                                                                </div>
                                                                <p className="text-sm font-medium text-gray-900 font-mono">{usuario.last_login_ip || '—'}</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            )}
                                        </Fragment>
                                    );
                                })}
                            </tbody>
                        </table>
                    </div>

                    {/* Paginação */}
                    {usuarios.last_page > 1 && (
                        <div className="flex items-center justify-between border-t border-gray-200 bg-gray-50 px-4 py-3">
                            <p className="text-sm text-gray-500">
                                Página {usuarios.current_page} de {usuarios.last_page} ({usuarios.total} registros)
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
            )}
        </div>
    );
}
