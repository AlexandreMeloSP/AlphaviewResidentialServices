export interface User {
    id: number;
    name: string;
    email: string;
    cpf?: string;
    status: 'pending' | 'approved' | 'rejected';
    is_admin: boolean;
    email_verified_at: string | null;
    mfa_enabled?: boolean;
    accepted_terms_at?: string | null;
    terms_version?: string | null;
    deleted_at?: string | null;
    last_login_at?: string | null;
    last_login_ip?: string | null;
    profile?: Profile;
    services_count?: number;
    created_at: string;
    updated_at: string;
}

export interface Profile {
    id: number;
    user_id: number;
    bio?: string;
    avatar?: string;
    phone?: string;
    address?: string;
    created_at: string;
    updated_at: string;
}

export interface Service {
    id: number;
    user_id: number;
    titulo: string;
    descricao: string;
    categoria: string;
    valor_sugerido?: number;
    imagem?: string;
    status: 'active' | 'inactive';
    created_at: string;
    updated_at: string;
    user?: User;
}

export interface Message {
    id: number;
    sender_id: number;
    receiver_id: number;
    conteudo: string;
    lida: boolean;
    created_at: string;
    updated_at: string;
    sender?: User;
    receiver?: User;
}

export interface Exchange {
    id: number;
    service_proponente_id: number;
    service_receptor_id: number;
    user_proponente_id: number;
    user_receptor_id: number;
    status: 'pending' | 'confirmed' | 'completed' | 'cancelled';
    created_at: string;
    updated_at: string;
    service_proponente?: Service;
    service_receptor?: Service;
    user_proponente?: User;
    user_receptor?: User;
}

export interface Contract {
    id: number;
    exchange_id: number;
    user_1_id: number;
    user_2_id: number;
    conteudo_pdf?: string;
    assinatura_1_at: string | null;
    assinatura_2_at: string | null;
    pdf_path?: string;
    status: 'draft' | 'pending' | 'signed' | 'cancelled';
    created_at: string;
    updated_at: string;
    exchange?: Exchange;
    user1?: User;
    user2?: User;
}

export interface ApiResponse<T> {
    data: T;
    message?: string;
}

export interface PaginatedResponse<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}
