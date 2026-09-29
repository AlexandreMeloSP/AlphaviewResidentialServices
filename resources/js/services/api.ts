import type { ApiResponse, User } from '@/types';
import { API_BASE, ROUTE_PREFIX } from './config';
import { getXsrfToken } from '@/utils/getXsrfToken';
import { getTabId } from '@/utils/getTabId';

async function request<T>(url: string, options?: RequestInit): Promise<T> {
    const response = await fetch(`${API_BASE}${url}`, {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-XSRF-TOKEN': getXsrfToken(),
            'X-TAB-ID': getTabId(),
            ...options?.headers,
        },
        credentials: 'same-origin',
        ...options,
    });

    if (response.status === 401) {
        window.location.href = `${ROUTE_PREFIX}/entrar`;
        throw new Error('Não autenticado');
    }

    if (!response.ok) {
        const error = await response.json().catch(() => ({ message: 'Erro desconhecido' }));
        let errorMessage = error.message || 'Erro na requisição';
        if (error.errors) {
            const firstError = Object.values(error.errors)[0];
            if (Array.isArray(firstError) && firstError.length > 0) {
                errorMessage = firstError[0];
            }
        }
        const err = new Error(errorMessage);
        (err as any).status = response.status;
        (err as any).data = error;
        throw err;
    }

    return response.json();
}

export async function getCsrfCookie(): Promise<void> {
    await fetch(`${ROUTE_PREFIX}/sanctum/csrf-cookie`, { credentials: 'same-origin' });
}

export async function getUser(): Promise<ApiResponse<User>> {
    const res = await request<unknown>('/auth/user');
    return { data: res as unknown as User };
}

export interface LoginResponse {
    user?: User;
    requires_mfa?: boolean;
    message?: string;
    tab_id?: string;
}

export async function login(email: string, password: string): Promise<LoginResponse> {
    await getCsrfCookie();
    return request<LoginResponse>('/auth/login', {
        method: 'POST',
        body: JSON.stringify({ email, password }),
    });
}

export async function logout(): Promise<void> {
    await request('/auth/logout', { method: 'POST' });
}

export async function verifyMfa(code: string): Promise<{ user?: User; message?: string; tab_id?: string }> {
    return request<{ user?: User; message?: string; tab_id?: string }>('/auth/verify-mfa', {
        method: 'POST',
        body: JSON.stringify({ code }),
    });
}

export async function getProfile(): Promise<ApiResponse<unknown>> {
    const res = await request<unknown>('/profile');
    return { data: res };
}

export async function deleteProfile(): Promise<ApiResponse<unknown>> {
    const res = await request<unknown>('/profile', {
        method: 'DELETE',
    });
    return { data: res };
}

export async function changePassword(data: { current_password: string; password: string; password_confirmation: string }): Promise<ApiResponse<unknown>> {
    const res = await request<unknown>('/profile/password', {
        method: 'PUT',
        body: JSON.stringify(data),
    });
    return { data: res };
}

export async function getServicos(): Promise<ApiResponse<unknown[]>> {
    const res = await request<unknown>('/servicos');
    return { data: (res as Record<string, unknown>).data as unknown[] ?? res as unknown[] };
}

export async function getUsuarios(): Promise<ApiResponse<unknown[]>> {
    const res = await request<unknown>('/usuarios');
    return { data: (res as Record<string, unknown>).data as unknown[] ?? res as unknown[] };
}

export async function getUserDashboard(): Promise<ApiResponse<unknown>> {
    const res = await request<unknown>('/dashboard/user');
    return { data: res };
}

export async function getConversas(): Promise<ApiResponse<unknown[]>> {
    const res = await request<unknown>('/messages/conversations');
    return { data: Array.isArray(res) ? res : (res as Record<string, unknown>).data as unknown[] ?? [] };
}

export async function getMensagens(conversaId: number): Promise<ApiResponse<unknown[]>> {
    const res = await request<unknown>(`/messages/conversation/${conversaId}`);
    return { data: Array.isArray(res) ? res : (res as Record<string, unknown>).data as unknown[] ?? [] };
}

export async function enviarMensagem(receptorId: number, conteudo: string): Promise<ApiResponse<unknown>> {
    const res = await request<unknown>('/messages', {
        method: 'POST',
        body: JSON.stringify({ receiver_id: receptorId, content: conteudo }),
    });
    return { data: res };
}

export async function getContratos(): Promise<ApiResponse<unknown[]>> {
    const res = await request<unknown>('/contracts');
    return { data: Array.isArray(res) ? res : (res as Record<string, unknown>).data as unknown[] ?? [] };
}

export async function signContrato(id: number): Promise<unknown> {
    return request<unknown>(`/contracts/${id}/sign`, { method: 'POST' });
}

export async function updateContrato(id: number, data: Record<string, unknown>): Promise<unknown> {
    return request<unknown>(`/contracts/${id}`, { method: 'PUT', body: JSON.stringify(data) });
}

export async function generateContratoPdf(id: number): Promise<unknown> {
    return request<unknown>(`/contracts/${id}/pdf`, { method: 'POST' });
}

export async function downloadContratoPdf(id: number): Promise<Blob> {
    const response = await fetch(`${API_BASE}/contracts/${id}/download`, {
        headers: { 'X-XSRF-TOKEN': getXsrfToken(), 'X-TAB-ID': getTabId() },
        credentials: 'same-origin',
    });
    if (!response.ok) throw new Error('Erro ao baixar PDF');
    return response.blob();
}

export async function getMeusServicos(): Promise<ApiResponse<unknown[]>> {
    const res = await request<unknown>('/servicos/mine');
    return { data: (res as Record<string, unknown>).data as unknown[] ?? res as unknown[] };
}

export async function getServico(id: number): Promise<ApiResponse<unknown>> {
    const res = await request<unknown>(`/servicos/${id}`);
    return { data: res };
}

export async function createServico(data: Record<string, unknown>): Promise<unknown> {
    return request<unknown>('/servicos', { method: 'POST', body: JSON.stringify(data) });
}

export async function updateServico(id: number, data: Record<string, unknown>): Promise<unknown> {
    return request<unknown>(`/servicos/${id}`, { method: 'PUT', body: JSON.stringify(data) });
}

export async function deleteServico(id: number): Promise<unknown> {
    return request<unknown>(`/servicos/${id}`, { method: 'DELETE' });
}

export async function toggleServicoStatus(id: number): Promise<unknown> {
    return request<unknown>(`/servicos/${id}/toggle-status`, { method: 'PATCH' });
}

export async function getExchanges(): Promise<ApiResponse<unknown[]>> {
    const res = await request<unknown>('/exchanges');
    return { data: Array.isArray(res) ? res : (res as Record<string, unknown>).data as unknown[] ?? [] };
}

export async function getExchange(id: number): Promise<ApiResponse<unknown>> {
    const res = await request<unknown>(`/exchanges/${id}`);
    return { data: res };
}

export async function createExchange(data: Record<string, unknown>): Promise<unknown> {
    return request<unknown>('/exchanges', { method: 'POST', body: JSON.stringify(data) });
}

export async function confirmExchange(id: number): Promise<unknown> {
    return request<unknown>(`/exchanges/${id}/confirm`, { method: 'POST' });
}

export async function cancelExchange(id: number): Promise<unknown> {
    return request<unknown>(`/exchanges/${id}/cancel`, { method: 'POST' });
}

export async function forgotPassword(email: string): Promise<{ message: string }> {
    return request<{ message: string }>('/auth/forgot-password', {
        method: 'POST',
        body: JSON.stringify({ email }),
    });
}

export async function resetPassword(data: { token: string; email: string; password: string; password_confirmation: string }): Promise<{ message: string }> {
    return request<{ message: string }>('/auth/reset-password', {
        method: 'POST',
        body: JSON.stringify(data),
    });
}

// Admin
export async function getAdminRelatorios(): Promise<unknown> {
    return request<unknown>('/admin/relatorios');
}

export async function getAdminUsuarios(params?: Record<string, string>): Promise<unknown> {
    const qs = params ? `?${new URLSearchParams(params).toString()}` : '';
    return request<unknown>(`/admin/usuarios${qs}`);
}

export async function approveUsuario(id: number): Promise<unknown> {
    return request<unknown>(`/admin/usuarios/${id}/approve`, { method: 'POST' });
}

export async function rejectUsuario(id: number): Promise<unknown> {
    return request<unknown>(`/admin/usuarios/${id}/reject`, { method: 'POST' });
}

export async function toggleAdminUsuario(id: number): Promise<unknown> {
    return request<unknown>(`/admin/usuarios/${id}/toggle-admin`, { method: 'POST' });
}

export async function deleteUsuario(id: number): Promise<unknown> {
    return request<unknown>(`/admin/usuarios/${id}`, { method: 'DELETE' });
}

export async function restoreUsuario(id: number): Promise<unknown> {
    return request<unknown>(`/admin/usuarios/${id}/restore`, { method: 'POST' });
}

export async function forceDeleteUsuario(id: number): Promise<unknown> {
    return deleteUsuario(id);
}

export async function toggleMfa(password: string, code?: string): Promise<{ message: string; mfa_enabled: boolean; requires_code?: boolean }> {
    return request<{ message: string; mfa_enabled: boolean; requires_code?: boolean }>('/auth/toggle-mfa', {
        method: 'POST',
        body: JSON.stringify({ password, code }),
    });
}

export async function requestMfaCode(): Promise<{ message: string }> {
    return request<{ message: string }>('/auth/request-mfa-code', {
        method: 'POST',
    });
}
