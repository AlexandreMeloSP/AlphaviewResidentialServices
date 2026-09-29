@extends('layouts.landing')
@section('title', 'Servicos - Alphaview Serviços Residenciais')

@section('content')
<section class="pt-32 pb-16" style="background-color: #212529;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl sm:text-5xl font-bold text-white mb-4">Servicos Disponiveis</h1>
        <p class="text-lg text-gray-400 max-w-2xl mx-auto">Explore todos os servicos oferecidos pelos condôminos da plataforma.</p>
    </div>
</section>

<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row gap-4 mb-8">
            <div class="flex-1 relative">
                <input type="text" placeholder="Buscar servicos..." class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#f4623a] focus:border-transparent outline-none transition-all">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <select class="px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#f4623a] focus:border-transparent outline-none text-gray-700">
                <option value="">Todas as categorias</option>
                <option>Manutencao</option>
                <option>Saude e Bem-Estar</option>
                <option>Educacao</option>
                <option>Limpeza</option>
                <option>Tecnologia</option>
                <option>Eventos</option>
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($servicos as $servico)
            <div class="bg-gray-50 rounded-2xl p-6 hover:shadow-lg transition-all duration-300 border border-transparent hover:border-gray-100">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: #fff5f2;">
                        <svg class="w-5 h-5" style="color: #f4623a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full" style="background-color: #fff5f2; color: #f4623a;">{{ $servico->categoria }}</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $servico->titulo }}</h3>
                <p class="text-sm text-gray-500 mb-4">{{ Str::limit($servico->descricao, 100) }}</p>
                @if($servico->valor_sugerido)
                <p class="text-sm font-semibold mb-4" style="color: #f4623a;">R$ {{ number_format($servico->valor_sugerido, 2, ',', '.') }}</p>
                @endif
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold" style="background-color: #f4623a;">
                            {{ strtoupper(substr($servico->user->name ?? '?', 0, 1)) }}
                        </div>
                        <span class="text-xs text-gray-500">{{ $servico->user->name ?? 'Condômino' }}</span>
                    </div>
                    <a href="{{ $base }}/usuarios" class="text-sm font-medium hover:underline" style="color: #f4623a;">Ver perfil</a>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 text-lg">Nenhum serviço disponível no momento.</p>
                <p class="text-gray-400 text-sm mt-2">Seja o primeiro a cadastrar um serviço!</p>
            </div>
            @endforelse
        </div>

        @if($servicos->hasPages())
        <div class="mt-8">
            {{ $servicos->links() }}
        </div>
        @endif
    </div>
</section>
@endsection
