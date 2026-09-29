import { useState, useEffect } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { deleteProfile, changePassword, toggleMfa, requestMfaCode } from '@/services/api';
import { API_BASE, storageUrl } from '@/services/config';
import { formatCpf } from '@/utils/formatCpf';
import { getXsrfToken } from '@/utils/getXsrfToken';
import { getTabId } from '@/utils/getTabId';

export default function Perfil() {
    const { user, refreshUser, logout } = useAuth();
    const [editando, setEditando] = useState(false);
    const [salvando, setSalvando] = useState(false);
    const [mensagem, setMensagem] = useState('');
    const [showDeleteConfirm, setShowDeleteConfirm] = useState(false);
    const [deletando, setDeletando] = useState(false);
    const [avatarFile, setAvatarFile] = useState<File | null>(null);
    const [avatarPreview, setAvatarPreview] = useState<string | null>(null);
    const [form, setForm] = useState({
        name: user?.name || '',
        email: user?.email || '',
    });
    const [emailPassword, setEmailPassword] = useState('');

    const [showPasswordForm, setShowPasswordForm] = useState(false);
    const [senhaForm, setSenhaForm] = useState({
        current_password: '',
        password: '',
        password_confirmation: '',
    });
    const [senhaSalvando, setSenhaSalvando] = useState(false);
    const [senhaMensagem, setSenhaMensagem] = useState('');

    const [showMfaForm, setShowMfaForm] = useState(false);
    const [mfaPassword, setMfaPassword] = useState('');
    const [mfaCode, setMfaCode] = useState('');
    const [mfaSalvando, setMfaSalvando] = useState(false);
    const [mfaMensagem, setMfaMensagem] = useState('');
    const [enviandoCodigo, setEnviandoCodigo] = useState(false);

    useEffect(() => {
        return () => { if (avatarPreview) URL.revokeObjectURL(avatarPreview); };
    }, [avatarPreview]);

    const handleAvatarChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                setMensagem('Tipo de arquivo não permitido. Use JPG, PNG ou WebP.');
                e.target.value = '';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                setMensagem('A imagem deve ter no máximo 5MB.');
                e.target.value = '';
                return;
            }
            setAvatarFile(file);
            setAvatarPreview(URL.createObjectURL(file));
        }
    };

    const handleSalvar = async () => {
        setSalvando(true);
        setMensagem('');
        try {
            const fd = new FormData();
            fd.append('_method', 'PUT');
            fd.append('name', form.name);
            if (form.email !== user?.email) {
                fd.append('email', form.email);
                fd.append('current_password', emailPassword);
            }
            if (avatarFile) {
                fd.append('avatar', avatarFile);
            }

            const res = await fetch(`${API_BASE}/profile`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': getXsrfToken(), 'X-TAB-ID': getTabId() },
                body: fd,
            });

            if (!res.ok) {
                const errData = await res.json().catch(() => ({}));
                throw new Error(errData.message || 'Erro ao atualizar perfil.');
            }

            await refreshUser();
            setMensagem(form.email !== user?.email 
                ? 'Perfil atualizado! Um link de confirmação foi enviado para seu novo e-mail.' 
                : 'Perfil atualizado com sucesso!');
            setEditando(false);
            setEmailPassword('');
            setAvatarFile(null);
            setAvatarPreview(null);
        } catch (e: unknown) {
            const err = e as Error;
            setMensagem(err.message || 'Erro ao atualizar perfil.');
        } finally {
            setSalvando(false);
        }
    };

    const handleSolicitarCodigo = async () => {
        setEnviandoCodigo(true);
        setMfaMensagem('');
        try {
            const res = await requestMfaCode();
            setMfaMensagem(res.message);
        } catch (e: unknown) {
            const err = e as { data?: { message?: string } };
            setMfaMensagem(err.data?.message || 'Erro ao enviar código de verificação.');
        } finally {
            setEnviandoCodigo(false);
        }
    };

    const handlePasswordChange = async () => {
        setSenhaSalvando(true);
        setSenhaMensagem('');
        try {
            await changePassword(senhaForm);
            setSenhaMensagem('Senha atualizada com sucesso!');
            setSenhaForm({ current_password: '', password: '', password_confirmation: '' });
            setShowPasswordForm(false);
        } catch (e: unknown) {
            const err = e as { data?: { message?: string } };
            setSenhaMensagem(err.data?.message || 'Erro ao alterar senha. Verifique os requisitos.');
        } finally {
            setSenhaSalvando(false);
        }
    };

    const handleDelete = async () => {
        setDeletando(true);
        try {
            await deleteProfile();
            await logout();
        } catch {
            setMensagem('Erro ao excluir conta.');
            setDeletando(false);
        }
    };

    const handleMfaToggle = async () => {
        setMfaSalvando(true);
        setMfaMensagem('');
        try {
            const result = await toggleMfa(mfaPassword, user?.mfa_enabled ? mfaCode : undefined);
            setMfaMensagem(result.message);
            await refreshUser();
            setMfaPassword('');
            setMfaCode('');
            setShowMfaForm(false);
        } catch (e: unknown) {
            const err = e as { data?: { message?: string } };
            setMfaMensagem(err.data?.message || 'Erro ao alterar autenticação em duas etapas.');
        } finally {
            setMfaSalvando(false);
        }
    };

    const avatarSrc = avatarPreview || (user?.profile?.avatar ? storageUrl(user.profile.avatar) : null);

    return (
        <div className="mx-auto max-w-2xl space-y-6">
            <div>
                <h1 className="text-2xl font-bold text-gray-900">Meu Perfil</h1>
                <p className="mt-1 text-sm text-gray-500">Gerencie suas informações pessoais.</p>
            </div>

            {mensagem && (
                <div className={`rounded-lg px-4 py-3 text-sm ${
                    mensagem.includes('sucesso') ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'
                }`}>
                    {mensagem}
                </div>
            )}

            {user && !user.mfa_enabled && (
                <div className="rounded-xl border border-amber-200 bg-amber-50 p-4 flex items-start gap-3">
                    <div className="text-lg mt-0.5">⚠️</div>
                    <div>
                        <p className="text-sm font-semibold text-amber-900">Recomendação de segurança</p>
                        <p className="text-xs text-amber-700 mt-1">
                            Sua conta não possui autenticação em duas etapas (MFA) ativada. Ative na seção "Autenticação em Duas Etapas" abaixo para proteger seu login com um código extra enviado por email.
                        </p>
                    </div>
                </div>
            )}

            <div className="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div className="flex items-center gap-6">
                    <label className={`relative flex h-20 w-20 shrink-0 items-center justify-center rounded-full overflow-hidden group ${editando ? 'cursor-pointer' : ''}`}>
                        {avatarSrc ? (
                            <img src={avatarSrc} alt="Avatar" className="h-20 w-20 rounded-full object-cover" />
                        ) : (
                            <div className="flex h-20 w-20 items-center justify-center rounded-full bg-[#f4623a] text-2xl font-bold text-white">
                                {user?.name?.charAt(0).toUpperCase()}
                            </div>
                        )}
                        {editando && (
                            <>
                                <input
                                    type="file"
                                    accept="image/jpeg,image/png,image/webp"
                                    className="absolute inset-0 opacity-0"
                                    onChange={handleAvatarChange}
                                />
                                <div className="absolute inset-0 flex items-center justify-center rounded-full bg-black/40 opacity-0 transition-opacity group-hover:opacity-100">
                                    <span className="text-xs font-medium text-white">📷</span>
                                </div>
                            </>
                        )}
                    </label>
                    <div>
                        <h2 className="text-xl font-bold text-gray-900">{user?.name}</h2>
                        <p className="text-gray-500">{user?.email}</p>
                        <span className={`mt-2 inline-block rounded-full px-3 py-1 text-xs font-medium ${
                            user?.status === 'approved' ? 'bg-green-100 text-green-700' :
                            user?.status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                            'bg-red-100 text-red-700'
                        }`}>
                            {user?.status === 'approved' ? 'Aprovado' :
                             user?.status === 'pending' ? 'Pendente' : 'Rejeitado'}
                        </span>
                    </div>
                </div>

                <div className="mt-8 space-y-4">
                    <div>
                        <label className="mb-1 block text-sm font-medium text-gray-700">Nome</label>
                        <input
                            type="text"
                            value={form.name}
                            onChange={(e) => setForm({ ...form, name: e.target.value })}
                            disabled={!editando}
                            className="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 disabled:opacity-60 focus:border-[#f4623a] focus:outline-none focus:ring-1 focus:ring-[#f4623a]"
                        />
                    </div>
                    <div>
                        <label className="mb-1 block text-sm font-medium text-gray-700">E-mail</label>
                        <input
                            type="email"
                            value={form.email}
                            onChange={(e) => setForm({ ...form, email: e.target.value })}
                            disabled={!editando}
                            className="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 disabled:opacity-60 focus:border-[#f4623a] focus:outline-none focus:ring-1 focus:ring-[#f4623a]"
                        />
                    </div>
                    {editando && form.email !== user?.email && (
                        <div className="rounded-lg border border-amber-200 bg-amber-50 p-3">
                            <label className="mb-1 block text-xs font-semibold text-amber-900">
                                Senha atual (obrigatória para alterar e-mail)
                            </label>
                            <input
                                type="password"
                                value={emailPassword}
                                onChange={(e) => setEmailPassword(e.target.value)}
                                placeholder="Digite sua senha atual"
                                className="w-full rounded-lg border border-amber-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-[#f4623a] focus:outline-none focus:ring-1 focus:ring-[#f4623a]"
                            />
                            <p className="mt-1 text-xs text-amber-700">
                                Um link de confirmação será enviado para o novo endereço.
                            </p>
                        </div>
                    )}
                    <div>
                        <label className="mb-1 block text-sm font-medium text-gray-700">CPF</label>
                        <input
                            type="text"
                            value={formatCpf(user?.cpf)}
                            disabled
                            className="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 opacity-60"
                        />
                        <p className="mt-1 text-xs text-gray-400">CPF só pode ser alterado via fluxo administrativo.</p>
                    </div>
                </div>

                <div className="mt-6 flex gap-3">
                    {editando ? (
                        <>
                            <button
                                onClick={handleSalvar}
                                disabled={salvando}
                                className="rounded-lg bg-[#f4623a] px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-[#c34e2e] disabled:opacity-50"
                            >
                                {salvando ? 'Salvando...' : 'Salvar'}
                            </button>
                            <button
                                onClick={() => {
                                    setEditando(false);
                                    setForm({ name: user?.name || '', email: user?.email || '' });
                                    setAvatarFile(null);
                                    setAvatarPreview(null);
                                }}
                                className="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50"
                            >
                                Cancelar
                            </button>
                        </>
                    ) : (
                        <button
                            onClick={() => setEditando(true)}
                            className="rounded-lg border border-[#f4623a] px-5 py-2.5 text-sm font-medium text-[#f4623a] transition-colors hover:bg-[#f4623a]/5"
                        >
                            Editar Perfil
                        </button>
                    )}
                </div>
            </div>

            <div className="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 className="text-lg font-semibold text-gray-900">Alterar Senha</h2>
                <p className="mt-1 text-sm text-gray-500">
                    A senha deve ter no mínimo 8 caracteres, incluindo maiúsculas, minúsculas, números e caracteres especiais.
                </p>

                {senhaMensagem && (
                    <div className={`mt-3 rounded-lg px-4 py-3 text-sm ${
                        senhaMensagem.includes('sucesso') ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'
                    }`}>
                        {senhaMensagem}
                    </div>
                )}

                {!showPasswordForm ? (
                    <button
                        onClick={() => setShowPasswordForm(true)}
                        className="mt-4 rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50"
                    >
                        Alterar Senha
                    </button>
                ) : (
                    <div className="mt-4 space-y-4">
                        <div>
                            <label className="mb-1 block text-sm font-medium text-gray-700">Senha Atual</label>
                            <input
                                type="password"
                                value={senhaForm.current_password}
                                onChange={(e) => setSenhaForm({ ...senhaForm, current_password: e.target.value })}
                                className="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 focus:border-[#f4623a] focus:outline-none focus:ring-1 focus:ring-[#f4623a]"
                            />
                        </div>
                        <div>
                            <label className="mb-1 block text-sm font-medium text-gray-700">Nova Senha</label>
                            <input
                                type="password"
                                value={senhaForm.password}
                                onChange={(e) => setSenhaForm({ ...senhaForm, password: e.target.value })}
                                className="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 focus:border-[#f4623a] focus:outline-none focus:ring-1 focus:ring-[#f4623a]"
                            />
                        </div>
                        <div>
                            <label className="mb-1 block text-sm font-medium text-gray-700">Confirmar Nova Senha</label>
                            <input
                                type="password"
                                value={senhaForm.password_confirmation}
                                onChange={(e) => setSenhaForm({ ...senhaForm, password_confirmation: e.target.value })}
                                className="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 focus:border-[#f4623a] focus:outline-none focus:ring-1 focus:ring-[#f4623a]"
                            />
                        </div>
                        <div className="flex gap-3">
                            <button
                                onClick={handlePasswordChange}
                                disabled={senhaSalvando}
                                className="rounded-lg bg-[#f4623a] px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-[#c34e2e] disabled:opacity-50"
                            >
                                {senhaSalvando ? 'Salvando...' : 'Salvar Senha'}
                            </button>
                            <button
                                onClick={() => {
                                    setShowPasswordForm(false);
                                    setSenhaForm({ current_password: '', password: '', password_confirmation: '' });
                                    setSenhaMensagem('');
                                }}
                                className="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50"
                            >
                                Cancelar
                            </button>
                        </div>
                    </div>
                )}
            </div>

            <div className="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 className="text-lg font-semibold text-gray-900">Autenticação em Duas Etapas (MFA)</h2>
                <p className="mt-1 text-sm text-gray-500">
                    Adicione uma camada extra de segurança. Ao ativar, você precisará de um código enviado por email a cada login.
                </p>

                <div className="mt-4 flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                    <div>
                        <p className="text-sm font-medium text-gray-900">
                            Status: {user?.mfa_enabled ? (
                                <span className="text-green-600">Ativado</span>
                            ) : (
                                <span className="text-gray-500">Desativado</span>
                            )}
                        </p>
                    </div>
                    <button
                        onClick={() => {
                            setShowMfaForm(true);
                            setMfaMensagem('');
                            if (user?.mfa_enabled) {
                                handleSolicitarCodigo();
                            }
                        }}
                        className={`rounded-lg px-4 py-2 text-sm font-medium transition-colors ${
                            user?.mfa_enabled
                                ? 'border border-red-300 text-red-600 hover:bg-red-50'
                                : 'border border-[#f4623a] text-[#f4623a] hover:bg-[#f4623a]/5'
                        }`}
                    >
                        {user?.mfa_enabled ? 'Desativar' : 'Ativar'}
                    </button>
                </div>

                {mfaMensagem && (
                    <div className={`mt-3 rounded-lg px-4 py-3 text-sm ${
                        mfaMensagem.includes('sucesso') || mfaMensagem.includes('ativada') || mfaMensagem.includes('desativada')
                            ? 'bg-green-50 text-green-700'
                            : 'bg-red-50 text-red-700'
                    }`}>
                        {mfaMensagem}
                    </div>
                )}

                {showMfaForm && (
                    <div className="mt-4 rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <p className="text-sm text-gray-600 mb-3">
                            Confirme sua senha para {user?.mfa_enabled ? 'desativar' : 'ativar'} a autenticação em duas etapas.
                        </p>
                        <input
                            type="password"
                            value={mfaPassword}
                            onChange={(e) => setMfaPassword(e.target.value)}
                            placeholder="Senha atual"
                            className="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-[#f4623a] focus:outline-none focus:ring-1 focus:ring-[#f4623a]"
                        />
                        {user?.mfa_enabled && (
                            <div className="mt-3">
                                <div className="flex items-center justify-between mb-2">
                                    <p className="text-xs text-gray-500">
                                        Digite o código de 6 dígitos enviado para seu e-mail:
                                    </p>
                                    <button
                                        type="button"
                                        onClick={handleSolicitarCodigo}
                                        disabled={enviandoCodigo}
                                        className="text-xs font-semibold text-[#f4623a] hover:underline disabled:opacity-50"
                                    >
                                        {enviandoCodigo ? 'Enviando...' : 'Reenviar código'}
                                    </button>
                                </div>
                                <input
                                    type="text"
                                    value={mfaCode}
                                    onChange={(e) => setMfaCode(e.target.value)}
                                    placeholder="Código de 6 dígitos"
                                    maxLength={6}
                                    pattern="[0-9]{6}"
                                    className="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 text-center tracking-widest font-mono focus:border-[#f4623a] focus:outline-none focus:ring-1 focus:ring-[#f4623a]"
                                />
                            </div>
                        )}
                        <div className="mt-3 flex gap-3">
                            <button
                                onClick={handleMfaToggle}
                                disabled={mfaSalvando || !mfaPassword || (user?.mfa_enabled && !mfaCode)}
                                className={`rounded-lg px-5 py-2.5 text-sm font-medium text-white transition-colors disabled:opacity-50 ${
                                    user?.mfa_enabled
                                        ? 'bg-red-600 hover:bg-red-700'
                                        : 'bg-[#f4623a] hover:bg-[#c34e2e]'
                                }`}
                            >
                                {mfaSalvando ? 'Processando...' : 'Confirmar'}
                            </button>
                            <button
                                onClick={() => { setShowMfaForm(false); setMfaPassword(''); setMfaCode(''); setMfaMensagem(''); }}
                                className="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50"
                            >
                                Cancelar
                            </button>
                        </div>
                    </div>
                )}
            </div>

            <div className="rounded-xl border border-red-200 bg-white p-6 shadow-sm">
                <h2 className="text-lg font-semibold text-red-900">Zona de Perigo</h2>
                <p className="mt-1 text-sm text-gray-500">
                    Ao excluir sua conta, ela será desativada por 7 dias. Após esse período, será excluída permanentemente.
                </p>

                {!showDeleteConfirm ? (
                    <button
                        onClick={() => setShowDeleteConfirm(true)}
                        className="mt-4 rounded-lg border border-red-300 px-5 py-2.5 text-sm font-medium text-red-600 transition-colors hover:bg-red-50"
                    >
                        Excluir minha conta
                    </button>
                ) : (
                    <div className="mt-4 rounded-lg border border-red-300 bg-red-50 p-4">
                        <p className="text-sm font-medium text-red-800">
                            Tem certeza? Sua conta será desativada e excluída permanentemente em 7 dias.
                        </p>
                        <div className="mt-3 flex gap-3">
                            <button
                                onClick={handleDelete}
                                disabled={deletando}
                                className="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-red-700 disabled:opacity-50"
                            >
                                {deletando ? 'Excluindo...' : 'Sim, excluir minha conta'}
                            </button>
                            <button
                                onClick={() => setShowDeleteConfirm(false)}
                                className="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50"
                            >
                                Cancelar
                            </button>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}
