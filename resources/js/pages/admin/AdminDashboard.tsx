import { useState, useEffect } from 'react';
import { getAdminRelatorios } from '@/services/api';

interface AdminStats {
    resumo: {
        total_usuarios: number;
        usuarios_aprovados: number;
        usuarios_pendentes: number;
        total_servicos: number;
        servicos_ativos: number;
        total_trocas: number;
        trocas_confirmadas: number;
        trocas_concluidas: number;
    };
    servicos_por_categoria: Array<{ categoria: string; total: number }>;
    usuarios_por_mes: Array<{ mes: string; total: number }>;
    servicos_por_mes: Array<{ mes: string; total: number }>;
}

export default function AdminDashboard() {
    const [stats, setStats] = useState<AdminStats | null>(null);
    const [carregando, setCarregando] = useState(true);

    useEffect(() => {
        getAdminRelatorios()
            .then((data) => setStats(data as AdminStats))
            .catch(() => {})
            .finally(() => setCarregando(false));
    }, []);

    if (carregando) {
        return (
            <div className="flex items-center justify-center py-12">
                <div className="h-8 w-8 animate-spin rounded-full border-4 border-[#f4623a] border-t-transparent" />
            </div>
        );
    }

    if (!stats) {
        return (
            <div className="rounded-xl border border-gray-200 bg-white p-12 text-center shadow-sm">
                <p className="text-sm text-gray-500">Erro ao carregar estatísticas.</p>
            </div>
        );
    }

    const cards = [
        { label: 'Total de Usuários', value: stats.resumo.total_usuarios, icon: '👥', color: 'bg-blue-500' },
        { label: 'Usuários Aprovados', value: stats.resumo.usuarios_aprovados, icon: '✅', color: 'bg-green-500' },
        { label: 'Pendentes', value: stats.resumo.usuarios_pendentes, icon: '⏳', color: 'bg-yellow-500' },
        { label: 'Total de Serviços', value: stats.resumo.total_servicos, icon: '🔧', color: 'bg-purple-500' },
        { label: 'Serviços Ativos', value: stats.resumo.servicos_ativos, icon: '📋', color: 'bg-indigo-500' },
        { label: 'Total de Trocas', value: stats.resumo.total_trocas, icon: '🔄', color: 'bg-orange-500' },
        { label: 'Trocas Confirmadas', value: stats.resumo.trocas_confirmadas, icon: '🤝', color: 'bg-teal-500' },
        { label: 'Trocas Concluídas', value: stats.resumo.trocas_concluidas, icon: '🎉', color: 'bg-pink-500' },
    ];

    return (
        <div className="space-y-6">
            <div>
                <h1 className="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
                <p className="mt-1 text-sm text-gray-500">Visão geral do sistema e estatísticas.</p>
            </div>

            {/* Cards */}
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {cards.map((card) => (
                    <div
                        key={card.label}
                        className="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-shadow hover:shadow-md"
                    >
                        <div className="flex items-center gap-4">
                            <div className={`flex h-12 w-12 shrink-0 items-center justify-center rounded-lg ${card.color} text-2xl text-white`}>
                                {card.icon}
                            </div>
                            <div>
                                <p className="text-sm font-medium text-gray-500">{card.label}</p>
                                <p className="mt-1 text-2xl font-bold text-gray-900">{card.value}</p>
                            </div>
                        </div>
                    </div>
                ))}
            </div>

            {/* Serviços por categoria */}
            <div className="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 className="mb-4 text-lg font-semibold text-gray-900">Serviços por Categoria</h2>
                {stats.servicos_por_categoria.length === 0 ? (
                    <p className="text-sm text-gray-500">Nenhum dado disponível.</p>
                ) : (
                    <div className="space-y-3">
                        {stats.servicos_por_categoria.map((cat) => (
                            <div key={cat.categoria} className="flex items-center gap-3">
                                <span className="w-32 text-sm text-gray-600 truncate">{cat.categoria}</span>
                                <div className="flex-1 h-4 rounded-full bg-gray-100 overflow-hidden">
                                    <div
                                        className="h-full bg-[#f4623a] rounded-full"
                                        style={{
                                            width: `${(cat.total / Math.max(...stats.servicos_por_categoria.map((c) => c.total))) * 100}%`,
                                        }}
                                    />
                                </div>
                                <span className="text-sm font-medium text-gray-900 w-8 text-right">{cat.total}</span>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            {/* Usuários por mês */}
            <div className="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div className="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 className="mb-4 text-lg font-semibold text-gray-900">Novos Usuários por Mês</h2>
                    {stats.usuarios_por_mes.length === 0 ? (
                        <p className="text-sm text-gray-500">Nenhum dado disponível.</p>
                    ) : (
                        <div className="space-y-2">
                            {stats.usuarios_por_mes.slice(0, 6).map((item) => (
                                <div key={item.mes} className="flex items-center justify-between text-sm">
                                    <span className="text-gray-600">{item.mes}</span>
                                    <span className="font-medium text-gray-900">{item.total}</span>
                                </div>
                            ))}
                        </div>
                    )}
                </div>

                <div className="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 className="mb-4 text-lg font-semibold text-gray-900">Novos Serviços por Mês</h2>
                    {stats.servicos_por_mes.length === 0 ? (
                        <p className="text-sm text-gray-500">Nenhum dado disponível.</p>
                    ) : (
                        <div className="space-y-2">
                            {stats.servicos_por_mes.slice(0, 6).map((item) => (
                                <div key={item.mes} className="flex items-center justify-between text-sm">
                                    <span className="text-gray-600">{item.mes}</span>
                                    <span className="font-medium text-gray-900">{item.total}</span>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
