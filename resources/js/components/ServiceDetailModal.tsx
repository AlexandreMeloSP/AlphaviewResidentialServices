import type { Service } from '@/types';
import { storageUrl } from '@/services/config';
import Avatar from '@/components/Avatar';

const CATEGORIA_ICONS: Record<string, string> = {
    'Encanador': '🔧', 'Eletricista': '⚡', 'Pintor': '🎨',
    'Marceneiro': '🪚', 'Jardinagem': '🌿', 'Limpeza': '🧹',
    'Manutenção': '🛠️', 'Tecnologia': '💻', 'Aulas': '📚', 'Outros': '📋',
};

interface ServiceDetailModalProps {
    servico: Service;
    onClose: () => void;
    showProvider?: boolean;
    actionButton?: React.ReactNode;
}

export default function ServiceDetailModal({ servico, onClose, showProvider = false, actionButton }: ServiceDetailModalProps) {
    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" onClick={onClose}>
            <div className="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl" onClick={(e) => e.stopPropagation()}>
                <div className="flex items-start justify-between">
                    <div>
                        <span className="inline-flex items-center gap-1 rounded-full bg-[#f4623a]/10 px-3 py-1 text-xs font-medium text-[#f4623a]">
                            {CATEGORIA_ICONS[servico.categoria] || '📋'} {servico.categoria || 'Geral'}
                        </span>
                        <h2 className="mt-2 text-xl font-bold text-gray-900">{servico.titulo}</h2>
                    </div>
                    <button
                        onClick={onClose}
                        className="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                    >
                        ✕
                    </button>
                </div>

                {servico.imagem && (
                    <img
                        src={storageUrl(servico.imagem)}
                        alt={servico.titulo}
                        className="mt-4 w-full rounded-lg object-cover max-h-64"
                    />
                )}

                <div className="mt-4 space-y-3">
                    <div>
                        <p className="text-xs font-medium text-gray-500 uppercase">Descrição</p>
                        <p className="mt-1 text-sm text-gray-700">{servico.descricao}</p>
                    </div>

                    {servico.valor_sugerido && (
                        <div>
                            <p className="text-xs font-medium text-gray-500 uppercase">Valor Sugerido</p>
                            <p className="mt-1 text-lg font-bold text-[#f4623a]">
                                R$ {Number(servico.valor_sugerido).toFixed(2)}
                            </p>
                        </div>
                    )}

                    {showProvider && servico.user && (
                        <div>
                            <p className="text-xs font-medium text-gray-500 uppercase">Prestador</p>
                            <div className="mt-1 flex items-center gap-2">
                                <Avatar name={servico.user.name} avatar={servico.user.profile?.avatar} size="xs" />
                                <span className="text-sm font-medium text-gray-900">{servico.user.name}</span>
                            </div>
                        </div>
                    )}

                    <div>
                        <p className="text-xs font-medium text-gray-500 uppercase">Status</p>
                        <span className={`mt-1 inline-block rounded-full px-3 py-1 text-xs font-medium ${
                            servico.status === 'active'
                                ? 'bg-green-100 text-green-700'
                                : 'bg-gray-100 text-gray-500'
                        }`}>
                            {servico.status === 'active' ? 'Ativo' : 'Inativo'}
                        </span>
                    </div>
                </div>

                <div className="mt-6 flex gap-3">
                    {actionButton}
                    <button
                        onClick={onClose}
                        className="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50"
                    >
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    );
}
