import { useState } from 'react';
import { Link, useNavigate, useSearchParams } from 'react-router-dom';
import type { User } from '@/types';
import { useAuth } from '@/hooks/useAuth';
import { verifyMfa } from '@/services/api';
import { setTabId } from '@/utils/getTabId';

export default function Login() {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [erro, setErro] = useState('');
    const [carregando, setCarregando] = useState(false);
    const { login, setUser } = useAuth();
    const navigate = useNavigate();
    const [searchParams] = useSearchParams();
    const redirectTo = searchParams.get('redirect');
    const safeRedirect = redirectTo && redirectTo.startsWith('/') && !redirectTo.startsWith('//')
        ? redirectTo
        : '/app/dashboard';

    const [mfaStep, setMfaStep] = useState(false);
    const [mfaCode, setMfaCode] = useState('');
    const [mfaErro, setMfaErro] = useState('');
    const [mfaCarregando, setMfaCarregando] = useState(false);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setErro('');
        setCarregando(true);

        try {
            const result = await login(email, password);
            if (result.requires_mfa) {
                setMfaStep(true);
                setMfaCode('');
            } else {
                navigate(safeRedirect);
            }
        } catch (err) {
            setErro(err instanceof Error ? err.message : 'Credenciais inválidas.');
        } finally {
            setCarregando(false);
        }
    };

    const handleMfaSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setMfaErro('');
        setMfaCarregando(true);

        try {
            const result = await verifyMfa(mfaCode);
            if (result.user) {
                setUser(result.user as User);
                if (result.tab_id) {
                    setTabId(result.tab_id);
                }
                navigate(safeRedirect);
            } else {
                setMfaErro(result.message || 'Código inválido.');
            }
        } catch (err) {
            setMfaErro(err instanceof Error ? err.message : 'Erro ao verificar código.');
        } finally {
            setMfaCarregando(false);
        }
    };

    const handleMfaBack = () => {
        setMfaStep(false);
        setMfaCode('');
        setMfaErro('');
    };

    return (
        <div className="flex min-h-screen bg-gray-50">
            {/* Lado esquerdo */}
            <div className="hidden w-1/2 items-center justify-center bg-[#212529] p-12 lg:flex">
                <div className="max-w-md text-center">
                    <h1 className="text-4xl font-bold text-white">
                        <span className="text-[#f4623a]">Alphaview</span>
                        <br />
                        <span className="text-white">Serviços</span>
                        <br />
                        <span className="text-white">Residenciais</span>
                    </h1>
                    <p className="mt-4 text-lg text-gray-400">
                        Plataforma de troca de serviços residenciais entre condôminos.
                    </p>
                </div>
            </div>

            {/* Lado direito - Formulário */}
            <div className="flex w-full items-center justify-center p-6 lg:w-1/2">
                <div className="w-full max-w-md space-y-8">
                    {!mfaStep ? (
                        <>
                            <div className="text-center">
                                <h2 className="text-2xl font-bold text-gray-900">Entrar</h2>
                                <p className="mt-2 text-sm text-gray-500">
                                    Acesse sua conta para gerenciar seus serviços.
                                </p>
                            </div>

                            {erro && (
                                <div className="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
                                    {erro}
                                </div>
                            )}

                            <form onSubmit={handleSubmit} className="space-y-5">
                                <div>
                                    <label htmlFor="email" className="mb-1 block text-sm font-medium text-gray-700">
                                        E-mail
                                    </label>
                                    <input
                                        id="email"
                                        type="email"
                                        required
                                        autoComplete="username"
                                        value={email}
                                        onChange={(e) => setEmail(e.target.value)}
                                        className="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 placeholder-gray-400 transition-colors focus:border-[#f4623a] focus:outline-none focus:ring-1 focus:ring-[#f4623a]"
                                        placeholder="seu@email.com"
                                    />
                                </div>
                                <div>
                                    <div className="flex items-center justify-between">
                                    <label htmlFor="password" className="text-sm font-medium text-gray-700">
                                        Senha
                                    </label>
                                    <Link to="/esqueci-senha" className="text-xs font-medium text-[#f4623a] hover:underline">
                                        Esqueceu a senha?
                                    </Link>
                                </div>
                                    <input
                                        id="password"
                                        type="password"
                                        required
                                        autoComplete="current-password"
                                        value={password}
                                        onChange={(e) => setPassword(e.target.value)}
                                        className="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 placeholder-gray-400 transition-colors focus:border-[#f4623a] focus:outline-none focus:ring-1 focus:ring-[#f4623a]"
                                        placeholder="••••••••"
                                    />
                                </div>
                                <button
                                    type="submit"
                                    disabled={carregando}
                                    className="w-full rounded-lg bg-[#f4623a] px-4 py-3 text-sm font-medium text-white transition-colors hover:bg-[#c34e2e] disabled:opacity-50"
                                >
                                    {carregando ? 'Entrando...' : 'Entrar'}
                                </button>
                            </form>

                            <p className="text-center text-sm text-gray-500">
                                Não tem uma conta?{' '}
                                <Link to="/cadastrar" className="font-medium text-[#f4623a] hover:underline">
                                    Cadastre-se
                                </Link>
                            </p>
                        </>
                    ) : (
                        <>
                            <div className="text-center">
                                <h2 className="text-2xl font-bold text-gray-900">Verificação em Duas Etapas</h2>
                                <p className="mt-2 text-sm text-gray-500">
                                    Digite o código de 6 dígitos enviado para seu email.
                                </p>
                            </div>

                            {mfaErro && (
                                <div className="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
                                    {mfaErro}
                                </div>
                            )}

                            <form onSubmit={handleMfaSubmit} className="space-y-5">
                                <div>
                                    <label htmlFor="mfa-code" className="mb-1 block text-sm font-medium text-gray-700">
                                        Código de verificação
                                    </label>
                                    <input
                                        id="mfa-code"
                                        type="text"
                                        required
                                        maxLength={6}
                                        pattern="[0-9]{6}"
                                        inputMode="numeric"
                                        autoComplete="one-time-code"
                                        value={mfaCode}
                                        onChange={(e) => setMfaCode(e.target.value.replace(/\D/g, ''))}
                                        className="w-full rounded-lg border border-gray-300 px-4 py-3 text-center text-2xl tracking-[0.5em] font-mono text-gray-900 placeholder-gray-400 transition-colors focus:border-[#f4623a] focus:outline-none focus:ring-1 focus:ring-[#f4623a]"
                                        placeholder="000000"
                                    />
                                    <p className="mt-2 text-xs text-gray-400 text-center">O código expira em 5 minutos.</p>
                                </div>
                                <button
                                    type="submit"
                                    disabled={mfaCarregando || mfaCode.length !== 6}
                                    className="w-full rounded-lg bg-[#f4623a] px-4 py-3 text-sm font-medium text-white transition-colors hover:bg-[#c34e2e] disabled:opacity-50"
                                >
                                    {mfaCarregando ? 'Verificando...' : 'Verificar Código'}
                                </button>
                                <button
                                    type="button"
                                    onClick={handleMfaBack}
                                    className="w-full text-sm font-medium text-gray-500 hover:text-gray-700"
                                >
                                    ← Voltar ao login
                                </button>
                            </form>
                        </>
                    )}
                </div>
            </div>
        </div>
    );
}
