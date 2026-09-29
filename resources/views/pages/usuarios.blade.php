@extends('layouts.landing')
@section('title', 'Usuarios - Alphaview Serviços Residenciais')

@section('content')
<section class="pt-32 pb-16" style="background-color: #212529;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl sm:text-5xl font-bold text-white mb-4">Nossos Usuarios</h1>
        <p class="text-lg text-gray-400 max-w-2xl mx-auto">Conheça os condôminos que ja fazem parte da nossa comunidade.</p>
    </div>
</section>

<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <div class="relative max-w-md">
                <input type="text" placeholder="Buscar usuarios..." class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#f4623a] focus:border-transparent outline-none transition-all">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($usuarios as $usuario)
            <div class="bg-gray-50 rounded-2xl p-6 hover:shadow-lg transition-all duration-300 text-center border border-transparent hover:border-gray-100">
                <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center text-white text-xl font-bold" style="background-color: #f4623a;">
                    {{ strtoupper(substr($usuario->name, 0, 1)) }}
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $usuario->name }}</h3>
                <p class="text-sm text-gray-500 mb-4">
                    @if($usuario->status === 'approved')
                        Condômino verificado
                    @else
                        Aguardando aprovação
                    @endif
                </p>
                <div class="flex justify-center gap-2 mb-4">
                    <span class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">Serviços: {{ $usuario->services_count ?? 0 }}</span>
                    @if($usuario->status === 'approved')
                        <span class="px-3 py-1 text-xs font-medium text-white rounded-full" style="background-color: #f4623a;">Ativo</span>
                    @else
                        <span class="px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Pendente</span>
                    @endif
                </div>
                @if($usuario->status === 'approved')
                <a href="#" class="inline-flex items-center justify-center gap-2 w-full py-2.5 text-sm font-semibold rounded-xl transition-all duration-300" style="background-color: #fff5f2; color: #f4623a;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Iniciar Conversa
                </a>
                @endif
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 text-lg">Nenhum usuário encontrado.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
