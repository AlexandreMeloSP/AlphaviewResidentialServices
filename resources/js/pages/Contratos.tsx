import { useState, useEffect } from 'react';
import type { Contract, Exchange, User } from '@/types';
import { useAuth } from '@/hooks/useAuth';
import { getContratos, signContrato, updateContrato, generateContratoPdf, downloadContratoPdf } from '@/services/api';

interface ContractWithDetails extends Contract {
    exchange?: Exchange;
    user1?: User;
    user2?: User;
    observacao?: string;
}

export default function Contratos() {
    const { user } = useAuth();
    const [contratos, setContratos] = useState<ContractWithDetails[]>([]);
    const [carregando, setCarregando] = useState(true);
    const [filtro, setFiltro] = useState('all');

    useEffect(() => {
        getContratos()
            .then((res) => setContratos((res.data as ContractWithDetails[]) || []))
            .catch(() => {})
            .finally(() => setCarregando(false));
    }, []);

    const handleSign = async (id: number) => {
        if (!confirm('Tem certeza que deseja assinar este contrato?')) return;
        try {
            const data = await signContrato(id);
            setContratos((prev) => prev.map((c) => (c.id === id ? { ...c, ...data } : c)));
        } catch {}
    };

    const handleUpdateStatus = async (id: number, status: string, observacao: string) => {
        try {
            await updateContrato(id, { status, observacao });
            setContratos((prev) => prev.map((c) => (c.id === id ? { ...c, status: status as Contract['status'], observacao } : c)));
        } catch {}
    };

    const handleGeneratePdf = async (id: number) => {
        try {
            await generateContratoPdf(id);
            alert('Geração do PDF iniciada. Atualize a página em instantes.');
        } catch {}
    };

    const handleDownloadPdf = async (id: number) => {
        try {
            const blob = await downloadContratoPdf(id);
            const url = URL.createObjectURL(blob);
            window.open(url, '_blank');
        } catch {}
    };

    const statusColors: Record<string, string> = {
        signed: 'bg-green-100 text-green-700',
        pending: 'bg-yellow-100 text-yellow-700',
        draft: 'bg-blue-100 text-blue-700',
        rejected: 'bg-red-100 text-red-700',
        cancelled: 'bg-gray-100 text-gray-500',
    };

    const statusLabels: Record<string, string> = {
        signed: 'Assinado',
        pending: 'Aguardando assinatura',
        draft: 'Rascunho',
        rejected: 'Rejeitado',
        cancelled: 'Cancelado',
    };

    const contratosFiltrados = filtro === 'all'
        ? contratos
        : contratos.filter((c) => c.status === filtro);

    const filtros = [
        { value: 'all', label: 'Todos' },
        { value: 'signed', label: 'Assinados' },
        { value: 'pending', label: 'Não Assinados' },
        { value: 'rejected', label: 'Rejeitados' },
        { value: 'cancelled', label: 'Cancelados' },
    ];

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
                <h1 className="text-2xl font-bold text-gray-900">Contratos</h1>
                <p className="mt-1 text-sm text-gray-500">Acesse os contratos gerados para suas trocas de serviços.</p>
            </div>

            <div className="flex flex-wrap gap-2">
                {filtros.map((f) => (
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

            {contratosFiltrados.length === 0 ? (
                <div className="rounded-xl border border-gray-200 bg-white p-12 text-center shadow-sm">
                    <div className="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 text-3xl">📄</div>
                    <h2 className="mt-4 text-lg font-semibold text-gray-900">Nenhum contrato encontrado</h2>
                    <p className="mt-2 max-w-sm mx-auto text-sm text-gray-500">
                        {filtro === 'all'
                            ? 'Contratos são gerados automaticamente após a confirmação de trocas.'
                            : 'Nenhum contrato com este status.'}
                    </p>
                </div>
            ) : (
                <div className="space-y-4">
                    {contratosFiltrados.map((contrato) => {
                        const isUser1 = user?.id === contrato.user_1_id;
                        const outroUsuario = isUser1 ? contrato.user2 : contrato.user1;
                        const meuAssinado = isUser1 ? contrato.assinatura_1_at : contrato.assinatura_2_at;
                        const outroAssinado = isUser1 ? contrato.assinatura_2_at : contrato.assinatura_1_at;

                        return (
                            <div key={contrato.id} className="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                                <div className="flex items-start justify-between">
                                    <div>
                                        <p className="text-sm font-medium text-gray-900">Contrato #{contrato.id}</p>
                                        <p className="text-xs text-gray-500">Troca com {outroUsuario?.name}</p>
                                    </div>
                                    <span className={`rounded-full px-3 py-1 text-xs font-medium ${statusColors[contrato.status]}`}>
                                        {statusLabels[contrato.status]}
                                    </span>
                                </div>

                                <div className="mt-4 grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p className="text-gray-500">Sua assinatura:</p>
                                        <p className="font-medium text-gray-900">{meuAssinado ? 'Assinado' : 'Pendente'}</p>
                                    </div>
                                    <div>
                                        <p className="text-gray-500">Assinatura do outro:</p>
                                        <p className="font-medium text-gray-900">{outroAssinado ? 'Assinado' : 'Pendente'}</p>
                                    </div>
                                </div>

                                {contrato.observacao && (
                                    <div className="mt-3 rounded-lg bg-gray-50 p-3">
                                        <p className="text-xs font-medium text-gray-500">Observação</p>
                                        <p className="mt-1 text-sm text-gray-700">{contrato.observacao}</p>
                                    </div>
                                )}

                                <div className="mt-4 flex flex-wrap items-center gap-2 border-t border-gray-100 pt-3">
                                    {contrato.status !== 'signed' && !meuAssinado && (
                                        <button
                                            onClick={() => handleSign(contrato.id)}
                                            className="rounded-lg bg-[#f4623a] px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-[#e5512e]"
                                        >
                                            Assinar
                                        </button>
                                    )}
                                    {contrato.status === 'signed' && !contrato.pdf_path && (
                                        <button
                                            onClick={() => handleGeneratePdf(contrato.id)}
                                            className="rounded-lg bg-[#f4623a] px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-[#e5512e]"
                                        >
                                            Gerar PDF
                                        </button>
                                    )}
                                    {contrato.pdf_path && (
                                        <button
                                            onClick={() => handleDownloadPdf(contrato.id)}
                                            className="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-green-700"
                                        >
                                            Baixar PDF
                                        </button>
                                    )}
                                    {contrato.status === 'pending' && (
                                        <>
                                            <button
                                                onClick={() => {
                                                    const obs = prompt('Motivo da rejeição:') || '';
                                                    handleUpdateStatus(contrato.id, 'rejected', obs);
                                                }}
                                                className="rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-600 transition-colors hover:bg-red-50"
                                            >
                                                Rejeitar
                                            </button>
                                            <button
                                                onClick={() => {
                                                    const obs = prompt('Motivo do cancelamento:') || '';
                                                    handleUpdateStatus(contrato.id, 'cancelled', obs);
                                                }}
                                                className="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50"
                                            >
                                                Cancelar
                                            </button>
                                        </>
                                    )}
                                </div>
                            </div>
                        );
                    })}
                </div>
            )}
        </div>
    );
}
