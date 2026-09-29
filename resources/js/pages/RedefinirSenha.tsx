import { useState } from 'react';
import { Link, useParams, useNavigate } from 'react-router-dom';
import { resetPassword } from '@/services/api';

export default function RedefinirSenha() {
    const { token } = useParams<{ token: string }>();
    const navigate = useNavigate();
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [passwordConfirmation, setPasswordConfirmation] = useState('');
    const [erro, setErro] = useState('');
    const [sucesso, setSucesso] = useState(false);
    const [carregando, setCarregando] = useState(false);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setErro('');
        setCarregando(true);

        try {
            await resetPassword({
                token: token || '',
                email,
                password,
                password_confirmation: passwordConfirmation,
            });
            setSucesso(true);
            setTimeout(() => navigate('/entrar'), 3000);
        } catch (err: any) {
            setErro(err?.data?.message || err?.message || 'Erro ao redefinir senha.');
        } finally {
            setCarregando(false);
        }
    };

    return (
        <div className="flex min-h-screen bg-gray-50">
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

            <div className="flex w-full items-center justify-center p-6 lg:w-1/2">
                <div className="w-full max-w-md space-y-8">
                    <div className="text-center">
                        <h2 className="text-2xl font-bold text-gray-900">Redefinir Senha</h2>
                        <p className="mt-2 text-sm text-gray-500">
                            Crie uma nova senha para sua conta.
                        </p>
                    </div>

                    {erro && (
                        <div className="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">{erro}</div>
                    )}

                    {sucesso ? (
                        <div className="rounded-xl border border-green-200 bg-green-50 p-6 text-center">
                            <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100 text-2xl">✓</div>
                            <h3 className="mt-3 text-sm font-semibold text-green-900">Senha redefinida!</h3>
                            <p className="mt-1 text-sm text-green-700">
                                Redirecionando para o login...
                            </p>
                        </div>
                    ) : (
                        <form onSubmit={handleSubmit} className="space-y-5">
                            <div>
                                <label htmlFor="email" className="mb-1 block text-sm font-medium text-gray-700">
                                    E-mail
                                </label>
                                <input
                                    id="email"
                                    type="email"
                                    required
                                    value={email}
                                    onChange={(e) => setEmail(e.target.value)}
                                    className="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 placeholder-gray-400 transition-colors focus:border-[#f4623a] focus:outline-none focus:ring-1 focus:ring-[#f4623a]"
                                    placeholder="seu@email.com"
                                />
                            </div>
                            <div>
                                <label htmlFor="password" className="mb-1 block text-sm font-medium text-gray-700">
                                    Nova Senha
                                </label>
                                <input
                                    id="password"
                                    type="password"
                                    required
                                    minLength={8}
                                    value={password}
                                    onChange={(e) => setPassword(e.target.value)}
                                    className="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 placeholder-gray-400 transition-colors focus:border-[#f4623a] focus:outline-none focus:ring-1 focus:ring-[#f4623a]"
                                    placeholder="••••••••"
                                />
                                <p className="mt-1 text-xs text-gray-400">
                                    Mínimo 8 caracteres com maiúsculas, minúsculas, números e símbolos.
                                </p>
                            </div>
                            <div>
                                <label htmlFor="password_confirmation" className="mb-1 block text-sm font-medium text-gray-700">
                                    Confirmar Nova Senha
                                </label>
                                <input
                                    id="password_confirmation"
                                    type="password"
                                    required
                                    minLength={8}
                                    value={passwordConfirmation}
                                    onChange={(e) => setPasswordConfirmation(e.target.value)}
                                    className="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 placeholder-gray-400 transition-colors focus:border-[#f4623a] focus:outline-none focus:ring-1 focus:ring-[#f4623a]"
                                    placeholder="••••••••"
                                />
                            </div>
                            <button
                                type="submit"
                                disabled={carregando}
                                className="w-full rounded-lg bg-[#f4623a] px-4 py-3 text-sm font-medium text-white transition-colors hover:bg-[#c34e2e] disabled:opacity-50"
                            >
                                {carregando ? 'Redefinindo...' : 'Redefinir Senha'}
                            </button>
                        </form>
                    )}

                    <p className="text-center text-sm text-gray-500">
                        Lembrou a senha?{' '}
                        <Link to="/entrar" className="font-medium text-[#f4623a] hover:underline">
                            Entrar
                        </Link>
                    </p>
                </div>
            </div>
        </div>
    );
}
