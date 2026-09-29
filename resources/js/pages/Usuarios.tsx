import { useState, useEffect } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { useNavigate } from 'react-router-dom';
import type { User } from '@/types';
import { getUsuarios } from '@/services/api';
import Avatar from '@/components/Avatar';

export default function Usuarios() {
    const { user } = useAuth();
    const navigate = useNavigate();
    const [usuarios, setUsuarios] = useState<User[]>([]);
    const [busca, setBusca] = useState('');
    const [carregando, setCarregando] = useState(true);

    useEffect(() => {
        getUsuarios()
            .then((res) => setUsuarios(res.data as User[]))
            .catch(() => {})
            .finally(() => setCarregando(false));
    }, []);

    const usuariosFiltrados = usuarios.filter(
        (u) =>
            u.name?.toLowerCase().includes(busca.toLowerCase()) ||
            u.email?.toLowerCase().includes(busca.toLowerCase())
    );

    const handleIniciarConversa = (userId: number) => {
        if (!user) {
            navigate('/entrar');
            return;
        }
        if (user.status !== 'approved') {
            navigate('/app/mensagens?not_approved=1');
            return;
        }
        navigate(`/app/mensagens?user=${userId}`);
    };

    return (
        <div className="space-y-6">
            <div>
                <h1 className="text-2xl font-bold text-gray-900">Usuários</h1>
                <p className="mt-1 text-sm text-gray-500">
                    {user?.is_admin
                        ? 'Todos os usuários da plataforma.'
                        : 'Conheça os condôminos que oferecem serviços.'}
                </p>
            </div>

            {/* Busca */}
            <div className="relative">
                <input
                    type="text"
                    placeholder="Buscar usuários..."
                    value={busca}
                    onChange={(e) => setBusca(e.target.value)}
                    className="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 pl-10 text-sm text-gray-900 placeholder-gray-400 transition-colors focus:border-[#f4623a] focus:outline-none focus:ring-1 focus:ring-[#f4623a]"
                />
                <svg className="absolute left-3 top-3.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            {/* Lista */}
            {carregando ? (
                <div className="flex items-center justify-center py-12">
                    <div className="h-8 w-8 animate-spin rounded-full border-4 border-[#f4623a] border-t-transparent" />
                </div>
            ) : usuariosFiltrados.length === 0 ? (
                <div className="rounded-xl border border-gray-200 bg-white p-12 text-center shadow-sm">
                    <p className="text-lg font-medium text-gray-900">Nenhum usuário encontrado</p>
                    <p className="mt-1 text-sm text-gray-500">
                        {busca ? 'Tente buscar com outros termos.' : 'Ainda não há usuários aprovados.'}
                    </p>
                </div>
            ) : (
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {usuariosFiltrados.map((usuario) => (
                        <div
                            key={usuario.id}
                            className="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-shadow hover:shadow-md"
                        >
                            <div className="flex items-center gap-4">
                                <Avatar name={usuario.name} avatar={usuario.profile?.avatar} size="xl" />
                                <div className="min-w-0 flex-1">
                                    <h3 className="truncate text-lg font-semibold text-gray-900">{usuario.name}</h3>
                                    <p className="truncate text-sm text-gray-500">{usuario.email}</p>
                                </div>
                            </div>
                            <div className="mt-4 flex items-center justify-between">
                                <button
                                    onClick={() => handleIniciarConversa(usuario.id)}
                                    className="rounded-lg bg-[#f4623a] px-3 py-1.5 text-sm font-medium text-white transition-colors hover:bg-[#c34e2e]"
                                >
                                    Mensagem
                                </button>
                                <div className="flex items-center gap-2">
                                    <span className="px-2 py-0.5 text-xs font-medium bg-green-50 text-green-700 rounded-full">
                                        Ativo
                                    </span>
                                    <span className="text-xs text-gray-400">
                                        {usuario.services_count ?? 0} serviço(s)
                                    </span>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
