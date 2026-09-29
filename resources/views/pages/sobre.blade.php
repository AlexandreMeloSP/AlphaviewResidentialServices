@extends('layouts.landing')
@section('title', 'Sobre - Alphaview Serviços Residenciais')

@section('content')
<section class="pt-32 pb-16" style="background-color: #212529;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl sm:text-5xl font-bold text-white mb-4">Sobre o Alphaview</h1>
        <p class="text-lg text-gray-400 max-w-2xl mx-auto">Construindo pontes entre condôminos desde 2024.</p>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="prose prose-lg max-w-none">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Nossa Historia</h2>
            <p class="text-gray-600 leading-relaxed mb-6">
                O Alphaview Serviços Residenciais nasceu da necessidade real de facilitar a vida em comunidade.
                Percebemos que muitos condôminos possuem habilidades e servicos que poderiam beneficiar seus vizinhos,
                mas nao existia uma plataforma simples e segura para conectar essas pessoas.
            </p>
            <p class="text-gray-600 leading-relaxed mb-6">
                Nossa missao e criar uma rede de confianca dentro do residencial, onde cada morador pode
                oferecer e solicitar servicos de forma transparente, segura e sem burocracia.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mb-4 mt-10">Nossa Missao</h2>
            <p class="text-gray-600 leading-relaxed mb-6">
                Facilitar a troca de servicos entre condôminos, promovendo uma comunidade mais colaborativa,
                unida e sustentavel. Acreditamos que a proximidade geografica e um dos maiores ativos para
                construir relacoes de confianca e reciprocidade.
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mb-4 mt-10">Nossos Valores</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="font-bold text-gray-900 mb-2">Confianca</h3>
                    <p class="text-sm text-gray-600">Verificacao rigorosa de usuarios e aprovacao administrativa para garantir seguranca.</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="font-bold text-gray-900 mb-2">Comunidade</h3>
                    <p class="text-sm text-gray-600">Fortalecimento dos laços entre vizinhos e promocao do convívio harmonioso.</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="font-bold text-gray-900 mb-2">Transparencia</h3>
                    <p class="text-sm text-gray-600">Processos claros, contratos digitais e comunicacao aberta entre as partes.</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="font-bold text-gray-900 mb-2">Acessibilidade</h3>
                    <p class="text-sm text-gray-600">Plataforma gratuita e de facil uso para todos os condôminos.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
