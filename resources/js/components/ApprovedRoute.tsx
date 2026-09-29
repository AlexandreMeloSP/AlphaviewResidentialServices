import { Navigate, Outlet } from 'react-router-dom';
import { useAuth } from '@/hooks/useAuth';

export default function ApprovedRoute() {
    const { user, loading } = useAuth();

    if (loading) {
        return (
            <div className="flex h-screen items-center justify-center bg-gray-50">
                <div className="h-8 w-8 animate-spin rounded-full border-4 border-[#f4623a] border-t-transparent" />
            </div>
        );
    }

    if (!user) {
        return <Navigate to="/entrar" replace />;
    }

    if (user.status !== 'approved') {
        return <Navigate to="/app/dashboard" replace />;
    }

    return <Outlet />;
}
