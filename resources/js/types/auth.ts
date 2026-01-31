export type UserRole = 'admin' | 'buyer' | 'seller';

export type User = {
    id: number;
    name: string;
    email: string;
    phone?: string;
    whatsapp_number?: string;
    role: UserRole;
    avatar?: string;
    avatar_url?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};

export type Auth = {
    user: User;
};

export type TwoFactorConfigContent = {
    title: string;
    description: string;
    buttonText: string;
};
