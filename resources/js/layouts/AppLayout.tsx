import { useState } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { useAuth } from '@/hooks/useAuth';
import Avatar from '@/components/Avatar';

const NAV_ITEMS = [
    { path: '/app/perfil', label: 'Perfil', icon: '👤' },
    { path: '/app/dashboard', label: 'Dashboard', icon: '📊' },
    { path: '/app/servicos', label: 'Serviços', icon: '🔧' },
    { path: '/app/usuarios', label: 'Usuários', icon: '👥' },
    { path: '/app/mensagens', label: 'Mensagens', icon: '💬' },
    { path: '/app/trocas', label: 'Trocas', icon: '🔄' },
    { path: '/app/contratos', label: 'Contratos', icon: '📄' },
];

const ADMIN_ITEMS = [
    { path: '/app/admin', label: 'Admin Dashboard', icon: '⚙️' },
    { path: '/app/admin/usuarios', label: 'Gerenciar Usuários', icon: '👥' },
    { path: '/app/admin/aprovacoes', label: 'Aprovações', icon: '✅' },
    { path: '/app/admin/servicos', label: 'Gerenciar Serviços', icon: '🔧' },
];

export default function AppLayout({ children }: { children: React.ReactNode }) {
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const { user, logout } = useAuth();
    const location = useLocation();
    const navigate = useNavigate();

    const handleLogout = async () => {
        await logout();
        navigate('/entrar');
    };

    return (
        <div className="flex h-screen bg-gray-50">
            {/* Overlay mobile */}
            {sidebarOpen && (
                <div
                    className="fixed inset-0 z-30 bg-black/50 lg:hidden"
                    onClick={() => setSidebarOpen(false)}
                />
            )}

            {/* Sidebar */}
            <aside
                className={`fixed inset-y-0 left-0 z-40 w-64 transform bg-[#212529] text-white transition-transform duration-200 ease-in-out lg:static lg:translate-x-0 ${
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full'
                }`}
            >
                <div className="flex h-full flex-col">
                    {/* Logo */}
                    <div className="flex h-16 items-center justify-center border-b border-gray-700 px-4">
                        <span className="text-lg font-bold text-[#f4623a]">Alphaview</span>
                        <span className="ml-1 text-sm text-gray-400">RS</span>
                    </div>

                    {/* Navegação */}
                    <nav className="flex-1 overflow-y-auto px-3 py-4">
                        <div className="space-y-1">
                            {NAV_ITEMS.map((item) => {
                                const isActive = location.pathname === item.path;
                                return (
                                    <Link
                                        key={item.path}
                                        to={item.path}
                                        onClick={() => setSidebarOpen(false)}
                                        className={`flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-colors ${
                                            isActive
                                                ? 'bg-[#f4623a] text-white'
                                                : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                                        }`}
                                    >
                                        <span className="mr-3 text-lg">{item.icon}</span>
                                        {item.label}
                                    </Link>
                                );
                            })}
                        </div>

                        {user?.is_admin && (
                            <>
                                <div className="my-4 border-t border-gray-700" />
                                <p className="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Administração
                                </p>
                                <div className="space-y-1">
                                    {ADMIN_ITEMS.map((item) => {
                                        const isActive = location.pathname === item.path;
                                        return (
                                            <Link
                                                key={item.path}
                                                to={item.path}
                                                onClick={() => setSidebarOpen(false)}
                                                className={`flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-colors ${
                                                    isActive
                                                        ? 'bg-[#f4623a] text-white'
                                                        : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                                                }`}
                                            >
                                                <span className="mr-3 text-lg">{item.icon}</span>
                                                {item.label}
                                            </Link>
                                        );
                                    })}
                                </div>
                            </>
                        )}
                    </nav>

                    {/* Usuário */}
                    <div className="border-t border-gray-700 p-4">
                        <div className="flex items-center">
                            <Avatar name={user?.name} avatar={user?.profile?.avatar} size="md" />
                            <div className="ml-3 flex-1 truncate">
                                <p className="truncate text-sm font-medium text-white">{user?.name}</p>
                                <p className="truncate text-xs text-gray-400">{user?.email}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            {/* Conteúdo principal */}
            <div className="flex flex-1 flex-col overflow-hidden">
                {/* Header */}
                <header className="flex h-16 items-center justify-between border-b border-gray-200 bg-white px-4 shadow-sm lg:px-6">
                    {/* Menu mobile */}
                    <button
                        onClick={() => setSidebarOpen(true)}
                        className="rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:hidden"
                    >
                        <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div className="flex-1" />

                    <div className="flex items-center gap-3">
                        <Link
                            to="/app/perfil"
                            className="hidden items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 sm:flex"
                        >
                            <span className="font-medium">{user?.name}</span>
                        </Link>
                        <button
                            onClick={handleLogout}
                            className="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-600 transition-colors hover:bg-gray-200"
                        >
                            Sair
                        </button>
                    </div>
                </header>

                {/* Conteúdo */}
                <main className="flex-1 overflow-y-auto p-4 lg:p-6">
                    {user?.status === 'pending' && (
                        <div className="mb-4 p-4 rounded-xl border border-yellow-200 bg-yellow-50 flex items-center gap-3">
                            <span className="text-2xl">&#9888;</span>
                            <div>
                                <p className="text-sm font-semibold text-yellow-800">Conta aguardando aprovacao</p>
                                <p className="text-xs text-yellow-600">Sua conta foi verificada, mas ainda precisa de aprovacao do administrador para acessar todos os recursos.</p>
                            </div>
                        </div>
                    )}
                    {children}
                </main>
            </div>
        </div>
    );
}
