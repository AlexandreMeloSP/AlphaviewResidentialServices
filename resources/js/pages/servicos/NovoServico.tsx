import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { API_BASE } from '@/services/config';
import { getXsrfToken } from '@/utils/getXsrfToken';
import { getTabId } from '@/utils/getTabId';

export default function NovoServico() {
    const navigate = useNavigate();
    const [form, setForm] = useState({
        titulo: '',
        descricao: '',
        categoria: '',
        valor_sugerido: '',
    });
    const [imagem, setImagem] = useState<File | null>(null);
    const [erros, setErros] = useState<Record<string, string>>({});
    const [enviando, setEnviando] = useState(false);

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

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
        setForm({ ...form, [e.target.name]: e.target.value });
        if (erros[e.target.name]) {
            setErros({ ...erros, [e.target.name]: '' });
        }
    };

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                setErros({ ...erros, imagem: 'Tipo de arquivo não permitido. Use JPG, PNG ou WebP.' });
                e.target.value = '';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                setErros({ ...erros, imagem: 'A imagem deve ter no máximo 5MB.' });
                e.target.value = '';
                return;
            }
        }
        setImagem(file ?? null);
        if (erros.imagem) {
            setErros({ ...erros, imagem: '' });
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setEnviando(true);
        setErros({});

        const formData = new FormData();
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
            const res = await fetch(`${API_BASE}/servicos`, {
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
            setErros({ titulo: 'Erro ao cadastrar serviço.' });
        } finally {
            setEnviando(false);
        }
    };

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
                <h1 className="mt-2 text-2xl font-bold text-gray-900">Novo Serviço</h1>
                <p className="mt-1 text-sm text-gray-500">Cadastre um novo serviço para trocar com outros condôminos.</p>
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
                        placeholder="Ex: Reparo de encanamento"
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
                        placeholder="Descreva o serviço oferecido..."
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
                        placeholder="0.00"
                    />
                    {erros.valor_sugerido && <p className="mt-1 text-sm text-red-600">{erros.valor_sugerido}</p>}
                </div>

                <div>
                    <label htmlFor="imagem" className="block text-sm font-medium text-gray-700">
                        Imagem (opcional)
                    </label>
                    <input
                        type="file"
                        id="imagem"
                        accept="image/jpeg,image/png,image/webp"
                        onChange={handleFileChange}
                        className="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-[#f4623a]/10 file:px-4 file:py-2 file:text-sm file:font-medium file:text-[#f4623a] hover:file:bg-[#f4623a]/20"
                    />
                    <p className="mt-1 text-xs text-gray-400">Formatos aceitos: JPG, PNG ou WebP. Tamanho máximo: 5MB.</p>
                    {imagem && (
                        <p className="mt-1 text-xs text-gray-500">
                            Arquivo: {imagem.name} ({(imagem.size / 1024 / 1024).toFixed(2)} MB)
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
                        {enviando ? 'Cadastrando...' : 'Cadastrar Serviço'}
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
