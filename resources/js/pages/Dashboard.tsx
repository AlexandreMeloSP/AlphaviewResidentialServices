import { useState, useEffect } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { Link } from 'react-router-dom';
import { getUserDashboard } from '@/services/api';

interface DashboardData {
    meus_servicos: number;
    servicos_restantes: number;
    mensagens_count: number;
    trocas_count: number;
    contratos_count: number;
    servicos_recentes: Array<{ id: number; titulo: string; categoria: string; user: { name: string } }>;
}

export default function Dashboard() {
    const { user } = useAuth();
    const [stats, setStats] = useState<DashboardData | null>(null);

    useEffect(() => {
        if (user) {
            getUserDashboard()
                .then((res) => setStats(res.data as DashboardData))
                .catch(() => {});
        }
    }, [user]);

    const isApproved = user?.status === 'approved';
    const isVerified = !!user?.email_verified_at;
    const isPending = user?.status === 'pending';

    return (
        <div className="space-y-6">
            <div>
                <h1 className="text-2xl font-bold text-gray-900">
                    Olá, {user?.name?.split(' ')[0]}!
                </h1>
                <p className="mt-1 text-gray-500">
                    Bem-vindo ao painel do Alphaview Serviços Residenciais.
                </p>
            </div>

            {!isVerified && (
                <div className="rounded-xl border border-red-200 bg-red-50 p-5">
                    <div className="flex items-start gap-3">
                        <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-100 text-lg">📧</div>
                        <div>
                            <h3 className="text-sm font-semibold text-red-900">Email não verificado</h3>
                            <p className="mt-1 text-sm text-red-700">
                                Verifique sua caixa de entrada para ativar sua conta.
                            </p>
                        </div>
                    </div>
                </div>
            )}

            {isPending && isVerified && (
                <div className="rounded-xl border border-amber-200 bg-amber-50 p-5">
                    <div className="flex items-start gap-3">
                        <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-lg">⏳</div>
                        <div>
                            <h3 className="text-sm font-semibold text-amber-900">Conta aguardando aprovação</h3>
                            <p className="mt-1 text-sm text-amber-700">
                                Seu email foi verificado. Aguarde o administrador aprovar sua conta para acessar todas as funcionalidades.
                            </p>
                        </div>
                    </div>
                </div>
            )}

            {/* Cards do usuário */}
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <Link to="/app/servicos" className="rounded-xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div className="flex items-center gap-4">
                        <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-blue-500 text-2xl text-white">🔧</div>
                        <div>
                            <p className="text-sm font-medium text-gray-500">Meus Serviços</p>
                            <p className="mt-1 text-2xl font-bold text-gray-900">{stats?.meus_servicos ?? 0}</p>
                        </div>
                    </div>
                </Link>
                <Link to="/app/servicos/meus" className="rounded-xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div className="flex items-center gap-4">
                        <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-green-500 text-2xl text-white">📦</div>
                        <div>
                            <p className="text-sm font-medium text-gray-500">Serviços Restantes</p>
                            <p className="mt-1 text-2xl font-bold text-gray-900">{stats?.servicos_restantes ?? 5}</p>
                        </div>
                    </div>
                </Link>
                <Link to="/app/mensagens" className="rounded-xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div className="flex items-center gap-4">
                        <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-yellow-500 text-2xl text-white">💬</div>
                        <div>
                            <p className="text-sm font-medium text-gray-500">Mensagens</p>
                            <p className="mt-1 text-2xl font-bold text-gray-900">{stats?.mensagens_count ?? 0}</p>
                        </div>
                    </div>
                </Link>
                <Link to="/app/trocas" className="rounded-xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div className="flex items-center gap-4">
                        <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-2xl text-white">🔄</div>
                        <div>
                            <p className="text-sm font-medium text-gray-500">Trocas</p>
                            <p className="mt-1 text-2xl font-bold text-gray-900">{stats?.trocas_count ?? 0}</p>
                        </div>
                    </div>
                </Link>
                <Link to="/app/contratos" className="rounded-xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div className="flex items-center gap-4">
                        <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-purple-500 text-2xl text-white">📄</div>
                        <div>
                            <p className="text-sm font-medium text-gray-500">Contratos</p>
                            <p className="mt-1 text-2xl font-bold text-gray-900">{stats?.contratos_count ?? 0}</p>
                        </div>
                    </div>
                </Link>
            </div>

            {/* Atalhos */}
            <div className="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 className="mb-4 text-lg font-semibold text-gray-900">Ações Rápidas</h2>
                <div className="grid grid-cols-2 gap-3">
                    <Link to="/app/perfil" className="flex items-center gap-3 rounded-lg border border-gray-100 p-4 hover:bg-gray-50 transition-colors">
                        <span className="text-xl">👤</span>
                        <span className="text-sm font-medium text-gray-700">Meu Perfil</span>
                    </Link>
                    <Link to="/app/usuarios" className="flex items-center gap-3 rounded-lg border border-gray-100 p-4 hover:bg-gray-50 transition-colors">
                        <span className="text-xl">👥</span>
                        <span className="text-sm font-medium text-gray-700">Ver Usuários</span>
                    </Link>
                    <Link to="/app/servicos" className="flex items-center gap-3 rounded-lg border border-gray-100 p-4 hover:bg-gray-50 transition-colors">
                        <span className="text-xl">🔍</span>
                        <span className="text-sm font-medium text-gray-700">Buscar Serviços</span>
                    </Link>
                    <Link to="/app/mensagens" className="flex items-center gap-3 rounded-lg border border-gray-100 p-4 hover:bg-gray-50 transition-colors">
                        <span className="text-xl">💬</span>
                        <span className="text-sm font-medium text-gray-700">Mensagens</span>
                    </Link>
                    <Link to="/app/trocas" className="flex items-center gap-3 rounded-lg border border-gray-100 p-4 hover:bg-gray-50 transition-colors">
                        <span className="text-xl">🔄</span>
                        <span className="text-sm font-medium text-gray-700">Trocas</span>
                    </Link>
                    <Link to="/app/contratos" className="flex items-center gap-3 rounded-lg border border-gray-100 p-4 hover:bg-gray-50 transition-colors">
                        <span className="text-xl">📄</span>
                        <span className="text-sm font-medium text-gray-700">Contratos</span>
                    </Link>
                </div>
            </div>
        </div>
    );
}
