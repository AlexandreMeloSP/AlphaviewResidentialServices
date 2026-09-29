import "../css/app.css";
import React from "react";
import ReactDOM from "react-dom/client";
import { BrowserRouter, Routes, Route, Navigate, Outlet } from "react-router-dom";
import { AuthProvider } from "@/hooks/useAuth";
import ProtectedRoute from "@/components/ProtectedRoute";
import ApprovedRoute from "@/components/ApprovedRoute";
import AdminRoute from "@/components/AdminRoute";
import AppLayout from "@/layouts/AppLayout";
import Login from "@/pages/Login";
import Dashboard from "@/pages/Dashboard";
import Servicos from "@/pages/Servicos";
import Usuarios from "@/pages/Usuarios";
import Mensagens from "@/pages/Mensagens";
import Trocas from "@/pages/Trocas";
import Contratos from "@/pages/Contratos";
import Perfil from "@/pages/Perfil";
import EsqueciSenha from "@/pages/EsqueciSenha";
import RedefinirSenha from "@/pages/RedefinirSenha";
import MeusServicos from "@/pages/servicos/MeusServicos";
import NovoServico from "@/pages/servicos/NovoServico";
import EditarServico from "@/pages/servicos/EditarServico";
import AdminDashboard from "@/pages/admin/AdminDashboard";
import GerenciarUsuarios from "@/pages/admin/GerenciarUsuarios";
import Aprovacoes from "@/pages/admin/Aprovacoes";

function AdminPlaceholder({ title }: { title: string }) {
    return (
        <div className="space-y-6">
            <div>
                <h1 className="text-2xl font-bold text-gray-900">{title}</h1>
                <p className="mt-1 text-sm text-gray-500">Área administrativa em desenvolvimento.</p>
            </div>
            <div className="rounded-xl border border-gray-200 bg-white p-12 text-center shadow-sm">
                <div className="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 text-3xl">
                    ⚙️
                </div>
                <h2 className="mt-4 text-lg font-semibold text-gray-900">Em breve</h2>
                <p className="mt-2 max-w-sm mx-auto text-sm text-gray-500">
                    Esta área administrativa será disponibilizada em breve.
                </p>
            </div>
        </div>
    );
}

function AuthenticatedLayout() {
    return (
        <AppLayout>
            <Outlet />
        </AppLayout>
    );
}

function App() {
    return (
        <BrowserRouter basename="/alphaview">
            <AuthProvider>
                <Routes>
                    <Route path="/entrar" element={<Login />} />
                    <Route path="/esqueci-senha" element={<EsqueciSenha />} />
                    <Route path="/redefinir-senha/:token" element={<RedefinirSenha />} />

                    <Route element={<ProtectedRoute />}>
                        <Route element={<AuthenticatedLayout />}>
                            <Route path="/app" element={<Navigate to="/app/dashboard" replace />} />
                            <Route path="/app/dashboard" element={<Dashboard />} />

                            {/* Serviços */}
                            <Route path="/app/servicos" element={<Servicos />} />
                            <Route path="/app/servicos/meus" element={<MeusServicos />} />

                            <Route path="/app/usuarios" element={<Usuarios />} />
                            <Route path="/app/mensagens" element={<Mensagens />} />
                            <Route path="/app/trocas" element={<Trocas />} />
                            <Route path="/app/contratos" element={<Contratos />} />
                            <Route path="/app/perfil" element={<Perfil />} />

                            {/* Rotas que exigem aprovação */}
                            <Route element={<ApprovedRoute />}>
                                <Route path="/app/servicos/novo" element={<NovoServico />} />
                                <Route path="/app/servicos/editar/:id" element={<EditarServico />} />
                            </Route>

                            {/* Admin */}
                            <Route element={<AdminRoute />}>
                                <Route path="/app/admin" element={<AdminDashboard />} />
                                <Route path="/app/admin/usuarios" element={<GerenciarUsuarios />} />
                                <Route path="/app/admin/aprovacoes" element={<Aprovacoes />} />
                                <Route path="/app/admin/servicos" element={<AdminPlaceholder title="Gerenciar Serviços" />} />
                                <Route path="/app/admin/relatorios" element={<AdminPlaceholder title="Relatórios" />} />
                            </Route>
                        </Route>
                    </Route>

                    <Route path="*" element={<Navigate to="/app/dashboard" replace />} />
                </Routes>
            </AuthProvider>
        </BrowserRouter>
    );
}

ReactDOM.createRoot(document.getElementById("app")!).render(
    <React.StrictMode>
        <App />
    </React.StrictMode>
);
