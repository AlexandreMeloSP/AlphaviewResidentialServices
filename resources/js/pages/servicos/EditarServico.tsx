import { useState, useEffect } from 'react';
import { useNavigate, useParams, Link } from 'react-router-dom';
import type { Service } from '@/types';
import { getServico } from '@/services/api';
import { API_BASE, storageUrl } from '@/services/config';
import { getXsrfToken } from '@/utils/getXsrfToken';
import { getTabId } from '@/utils/getTabId';

export default function EditarServico() {
    const { id } = useParams<{ id: string }>();
    const navigate = useNavigate();
    const [form, setForm] = useState({
        titulo: '',
        descricao: '',
        categoria: '',
        valor_sugerido: '',
    });
    const [imagem, setImagem] = useState<File | null>(null);
    const [imagemAtual, setImagemAtual] = useState<string | null>(null);
    const [erros, setErros] = useState<Record<string, string>>({});
    const [enviando, setEnviando] = useState(false);
    const [carregando, setCarregando] = useState(true);

    const categorias = [
        'Encanador',
        'Eletricista',
        'Pintor',
        'Marceneiro',
        'Jardinagem',
        'Limpeza',
        'Manutenção',
        'Tecnologia',
        'Aulas',
        'Outros',
    ];

    useEffect(() => {
        getServico(Number(id))
            .then((res) => {
                const servico = (res.data as Service) || (res as unknown as Service);
                setForm({
                    titulo: servico.titulo || '',
                    descricao: servico.descricao || '',
                    categoria: servico.categoria || '',
                    valor_sugerido: servico.valor_sugerido?.toString() || '',
                });
                setImagemAtual(servico.imagem || null);
            })
            .catch(() => navigate('/app/servicos/meus'))
            .finally(() => setCarregando(false));
    }, [id, navigate]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
        setForm({ ...form, [e.target.name]: e.target.value });
        if (erros[e.target.name]) {
            setErros({ ...erros, [e.target.name]: '' });
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setEnviando(true);
        setErros({});

        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('titulo', form.titulo);
        formData.append('descricao', form.descricao);
        formData.append('categoria', form.categoria);
        if (form.valor_sugerido) {
            formData.append('valor_sugerido', form.valor_sugerido);
        }
        if (imagem) {
            formData.append('imagem', imagem);
        }

        try {
            const res = await fetch(`${API_BASE}/servicos/${id}`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': getXsrfToken(), 'X-TAB-ID': getTabId() },
                body: formData,
            });

            if (!res.ok) {
                const data = await res.json();
                if (data.errors) {
                    const formattedErrors: Record<string, string> = {};
                    Object.entries(data.errors).forEach(([key, value]) => {
                        formattedErrors[key] = (value as string[])[0];
                    });
                    setErros(formattedErrors);
                }
                return;
            }

            navigate('/app/servicos/meus');
        } catch {
            setErros({ titulo: 'Erro ao atualizar serviço.' });
        } finally {
            setEnviando(false);
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
        <div className="mx-auto max-w-2xl space-y-6">
            <div>
                <Link
                    to="/app/servicos/meus"
                    className="inline-flex items-center text-sm text-gray-500 hover:text-gray-700"
                >
                    <svg className="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                    </svg>
                    Voltar
                </Link>
                <h1 className="mt-2 text-2xl font-bold text-gray-900">Editar Serviço</h1>
                <p className="mt-1 text-sm text-gray-500">Atualize as informações do seu serviço.</p>
            </div>

            <form onSubmit={handleSubmit} className="rounded-xl border border-gray-200 bg-white p-6 shadow-sm space-y-5">
                <div>
                    <label htmlFor="titulo" className="block text-sm font-medium text-gray-700">
                        Título *
                    </label>
                    <input
                        type="text"
                        id="titulo"
                        name="titulo"
                        value={form.titulo}
                        onChange={handleChange}
                        className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-[#f4623a] focus:outline-none focus:ring-1 focus:ring-[#f4623a]"
                    />
                    {erros.titulo && <p className="mt-1 text-sm text-red-600">{erros.titulo}</p>}
                </div>

                <div>
                    <label htmlFor="descricao" className="block text-sm font-medium text-gray-700">
                        Descrição *
                    </label>
                    <textarea
                        id="descricao"
                        name="descricao"
                        value={form.descricao}
                        onChange={handleChange}
                        rows={4}
                        className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-[#f4623a] focus:outline-none focus:ring-1 focus:ring-[#f4623a]"
                    />
                    {erros.descricao && <p className="mt-1 text-sm text-red-600">{erros.descricao}</p>}
                </div>

                <div>
                    <label htmlFor="categoria" className="block text-sm font-medium text-gray-700">
                        Categoria *
                    </label>
                    <select
                        id="categoria"
                        name="categoria"
                        value={form.categoria}
                        onChange={handleChange}
                        className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm text-gray-900 focus:border-[#f4623a] focus:outline-none focus:ring-1 focus:ring-[#f4623a]"
                    >
                        <option value="">Selecione uma categoria</option>
                        {categorias.map((cat) => (
                            <option key={cat} value={cat}>{cat}</option>
                        ))}
                    </select>
                    {erros.categoria && <p className="mt-1 text-sm text-red-600">{erros.categoria}</p>}
                </div>

                <div>
                    <label htmlFor="valor_sugerido" className="block text-sm font-medium text-gray-700">
                        Valor Sugerido (R$)
                    </label>
                    <input
                        type="number"
                        id="valor_sugerido"
                        name="valor_sugerido"
                        value={form.valor_sugerido}
                        onChange={handleChange}
                        step="0.01"
                        min="0"
                        className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-[#f4623a] focus:outline-none focus:ring-1 focus:ring-[#f4623a]"
                    />
                    {erros.valor_sugerido && <p className="mt-1 text-sm text-red-600">{erros.valor_sugerido}</p>}
                </div>

                <div>
                    <label htmlFor="imagem" className="block text-sm font-medium text-gray-700">
                        Imagem (opcional)
                    </label>
                    {imagemAtual && !imagem && (
                        <div className="mt-1 mb-2">
                            <img
                                src={storageUrl(imagemAtual)}
                                alt="Imagem atual"
                                className="h-32 w-32 rounded-lg object-cover border border-gray-200"
                            />
                            <p className="mt-1 text-xs text-gray-400">Imagem atual. Selecione uma nova para substituir.</p>
                        </div>
                    )}
                    <input
                        type="file"
                        id="imagem"
                        accept="image/jpeg,image/png,image/webp"
                        onChange={(e) => setImagem(e.target.files?.[0] || null)}
                        className="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-[#f4623a]/10 file:px-4 file:py-2 file:text-sm file:font-medium file:text-[#f4623a] hover:file:bg-[#f4623a]/20"
                    />
                    <p className="mt-1 text-xs text-gray-400">Formatos aceitos: JPG, PNG ou WebP. Tamanho máximo: 5MB.</p>
                    {imagem && (
                        <p className="mt-1 text-xs text-gray-500">
                            Nova imagem: {imagem.name} ({(imagem.size / 1024 / 1024).toFixed(2)} MB)
                        </p>
                    )}
                    {erros.imagem && <p className="mt-1 text-sm text-red-600">{erros.imagem}</p>}
                </div>

                <div className="flex items-center gap-3 pt-4">
                    <button
                        type="submit"
                        disabled={enviando}
                        className="flex-1 rounded-lg bg-[#f4623a] px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-[#e5512e] disabled:opacity-50"
                    >
                        {enviando ? 'Salvando...' : 'Salvar Alterações'}
                    </button>
                    <Link
                        to="/app/servicos/meus"
                        className="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50"
                    >
                        Cancelar
                    </Link>
                </div>
            </form>
        </div>
    );
}
