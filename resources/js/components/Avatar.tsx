import { storageUrl } from '@/services/config';

interface AvatarProps {
    name?: string;
    avatar?: string | null;
    size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl';
    className?: string;
}

const sizeClasses = {
    xs: 'h-7 w-7 text-xs',
    sm: 'h-8 w-8 text-xs',
    md: 'h-9 w-9 text-sm',
    lg: 'h-10 w-10 text-sm',
    xl: 'h-12 w-12 text-lg',
    '2xl': 'h-20 w-20 text-2xl',
};

export default function Avatar({ name, avatar, size = 'md', className = '' }: AvatarProps) {
    const sizeClass = sizeClasses[size] || sizeClasses.md;

    if (avatar) {
        return (
            <img
                src={storageUrl(avatar)}
                alt={name || ''}
                className={`${sizeClass} shrink-0 rounded-full object-cover ${className}`}
            />
        );
    }

    return (
        <div className={`flex ${sizeClass} shrink-0 items-center justify-center rounded-full bg-[#f4623a] font-bold text-white ${className}`}>
            {name?.charAt(0).toUpperCase()}
        </div>
    );
}
