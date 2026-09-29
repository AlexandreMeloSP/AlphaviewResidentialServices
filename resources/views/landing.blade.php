@extends('layouts.landing')

@section('content')
<section id="inicio" class="relative min-h-screen flex items-center justify-center overflow-hidden" style="background: linear-gradient(135deg, #212529 0%, #2d3236 50%, #1a1d1f 100%);">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-20 left-10 w-72 h-72 rounded-full blur-3xl animate-pulse" style="background-color: #f4623a;"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 rounded-full blur-3xl" style="background-color: #f4623a; opacity: 0.5;"></div>
    </div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center py-32">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-sm text-white/80 text-sm mb-8 opacity-0 animate-[fadeUp_0.6s_ease-out_0.2s_forwards]">
            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
            Plataforma ativa em sua região
        </div>
        <h1 class="text-4xl sm:text-5xl md:text-7xl font-bold text-white leading-tight mb-6 opacity-0 animate-[fadeUp_0.6s_ease-out_0.4s_forwards]">
            Alphaview<br>
            <span class="text-transparent bg-clip-text" style="background-image: linear-gradient(135deg, #f4623a, #ff8a65);">Serviços Residenciais</span>
        </h1>
        <p class="text-lg sm:text-xl text-gray-300 max-w-2xl mx-auto mb-10 leading-relaxed opacity-0 animate-[fadeUp_0.6s_ease-out_0.6s_forwards]">
            Facilite a troca de serviços entre condôminos da sua residência.
            Conecte-se com vizinhos, compartilhe habilidades e construa uma comunidade mais colaborativa.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 opacity-0 animate-[fadeUp_0.6s_ease-out_0.8s_forwards]">
            <a href="{{ $base }}/cadastrar" target="_blank" class="inline-flex items-center px-8 py-4 rounded-full text-white font-semibold text-lg transition-all duration-300 hover:shadow-2xl hover:scale-105" style="background-color: #f4623a;">
                Cadastre-se Grátis
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
            <a href="#como-funciona" class="inline-flex items-center px-8 py-4 rounded-full text-white border border-white/30 font-semibold text-lg transition-all duration-300 hover:bg-white/10 hover:border-white/50">Saiba Mais</a>
        </div>
    </div>
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white" />
        </svg>
    </div>
</section>

<section id="sobre" class="py-24" style="background-color: #f8f9fa;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
            <p class="text-sm font-semibold uppercase tracking-wider mb-3" style="color: #f4623a;">Sobre Nós</p>
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-5">Conectando condôminos através de serviços</h2>
            <p class="text-base text-gray-500 max-w-2xl mx-auto leading-relaxed">Nossa plataforma permite que moradores compartilhem seus talentos de forma segura, transparente e completamente gratuita.</p>
        </div>
        <div class="max-w-4xl mx-auto text-center mb-16">
            <p class="text-base text-gray-500 leading-relaxed mb-5">
                O Alphaview Serviços Residenciais nasceu da necessidade de fortalecer a comunidade entre condôminos.
                Acreditamos que cada vizinho tem habilidades valiosas que podem beneficiar toda a residência.
            </p>
            <p class="text-base text-gray-500 leading-relaxed">
                Desde reparos domésticos até aulas particulares — tudo de forma segura, transparente e completamente gratuita.
            </p>
        </div>
        <div class="grid grid-cols-3 gap-8 max-w-lg mx-auto mb-16">
            <div class="text-center">
                <div class="text-4xl font-bold mb-1" style="color: #f4623a;">100%</div>
                <div class="text-base text-gray-500 font-medium">Gratuito</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold mb-1" style="color: #f4623a;">24h</div>
                <div class="text-base text-gray-500 font-medium">Disponível</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold mb-1" style="color: #f4623a;">5★</div>
                <div class="text-base text-gray-500 font-medium">Avaliação</div>
            </div>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-5 max-w-2xl mx-auto">
            <div class="bg-white rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                <div class="text-3xl mb-3">🔧</div>
                <div class="text-base font-semibold text-gray-900">Manutenção</div>
            </div>
            <div class="bg-white rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                <div class="text-3xl mb-3">📚</div>
                <div class="text-base font-semibold text-gray-900">Educação</div>
            </div>
            <div class="bg-white rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                <div class="text-3xl mb-3">🧹</div>
                <div class="text-base font-semibold text-gray-900">Limpeza</div>
            </div>
            <div class="bg-white rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                <div class="text-3xl mb-3">💻</div>
                <div class="text-base font-semibold text-gray-900">Tecnologia</div>
            </div>
        </div>
    </div>
</section>

<section id="como-funciona" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
            <p class="text-sm font-semibold uppercase tracking-wider mb-3" style="color: #f4623a;">Como Funciona</p>
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-5">Três passos simples</h2>
            <p class="text-base text-gray-500 max-w-xl mx-auto leading-relaxed">Conectar-se com seus vizinhos nunca foi tão fácil. Siga estes passos e comece a trocar serviços.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
            <div class="bg-gray-50 rounded-2xl p-8 text-center shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1 group">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-6 transition-transform duration-300 group-hover:scale-110" style="background-color: #fff5f2;">
                    <svg class="w-8 h-8" style="color: #f4623a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <div class="text-xs font-bold uppercase tracking-wider mb-3" style="color: #f4623a;">Passo 1</div>
                <h3 class="text-lg font-bold text-gray-900 mb-3">Cadastre-se</h3>
                <p class="text-base text-gray-500 leading-relaxed">Crie sua conta gratuita, verifique seu email e aguarde a aprovação do administrador.</p>
            </div>
            <div class="bg-gray-50 rounded-2xl p-8 text-center shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1 group">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-6 transition-transform duration-300 group-hover:scale-110" style="background-color: #fff5f2;">
                    <svg class="w-8 h-8" style="color: #f4623a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <div class="text-xs font-bold uppercase tracking-wider mb-3" style="color: #f4623a;">Passo 2</div>
                <h3 class="text-lg font-bold text-gray-900 mb-3">Encontre Serviços</h3>
                <p class="text-base text-gray-500 leading-relaxed">Navegue pelos serviços disponíveis, filtre por categoria e encontre o vizinho ideal.</p>
            </div>
            <div class="bg-gray-50 rounded-2xl p-8 text-center shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1 group">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-6 transition-transform duration-300 group-hover:scale-110" style="background-color: #fff5f2;">
                    <svg class="w-8 h-8" style="color: #f4623a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </div>
                <div class="text-xs font-bold uppercase tracking-wider mb-3" style="color: #f4623a;">Passo 3</div>
                <h3 class="text-lg font-bold text-gray-900 mb-3">Faça a Troca</h3>
                <p class="text-base text-gray-500 leading-relaxed">Inicie uma conversa, combine detalhes, confirme a troca e assine o contrato digital.</p>
            </div>
        </div>
    </div>
</section>

<section id="estatisticas" class="py-24" style="background-color: #f8f9fa;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
            <p class="text-sm font-semibold uppercase tracking-wider mb-3" style="color: #f4623a;">Estatísticas</p>
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-5">Números que comprovam</h2>
            <p class="text-base text-gray-500 max-w-xl mx-auto leading-relaxed">Nossa comunidade cresce a cada dia. Veja os resultados alcançados.</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 lg:gap-8">
            <div class="bg-white rounded-2xl p-8 text-center shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                <div class="text-4xl lg:text-5xl font-bold mb-3" style="color: #f4623a;">{{ $stats['usuarios'] }}</div>
                <div class="text-base text-gray-500 font-medium">Usuários Cadastrados</div>
            </div>
            <div class="bg-white rounded-2xl p-8 text-center shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                <div class="text-4xl lg:text-5xl font-bold mb-3" style="color: #f4623a;">{{ $stats['servicos'] }}</div>
                <div class="text-base text-gray-500 font-medium">Serviços Listados</div>
            </div>
            <div class="bg-white rounded-2xl p-8 text-center shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                <div class="text-4xl lg:text-5xl font-bold mb-3" style="color: #f4623a;">{{ $stats['trocas'] }}</div>
                <div class="text-base text-gray-500 font-medium">Trocas Realizadas</div>
            </div>
            <div class="bg-white rounded-2xl p-8 text-center shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                <div class="text-4xl lg:text-5xl font-bold mb-3" style="color: #f4623a;">{{ $stats['usuarios'] > 0 ? '100' : '0' }}%</div>
                <div class="text-base text-gray-500 font-medium">Satisfação</div>
            </div>
        </div>
    </div>
</section>

<section id="servicos" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
            <p class="text-sm font-semibold uppercase tracking-wider mb-3" style="color: #f4623a;">Serviços em Destaque</p>
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-5">Categorias populares</h2>
            <p class="text-base text-gray-500 max-w-xl mx-auto leading-relaxed">Explore as categorias mais procuradas na nossa comunidade de condôminos.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
            $servicosCards = [
            ['icon' => 'engrenagem', 'svg' => '
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />', 'titulo' => 'Manutenção', 'desc' => 'Eletricista, encanador, pedreiro, marceneiro e outros profissionais de reparo residencial.'],
            ['icon' => 'coracao', 'svg' => '
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />', 'titulo' => 'Saúde e Bem-Estar', 'desc' => 'Massagista, personal trainer, nutricionista e profissionais de saúde.'],
            ['icon' => 'livro', 'svg' => '
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />', 'titulo' => 'Educação', 'desc' => 'Professores particulares, tutores, idiomas, música e cursos.'],
            ['icon' => 'predio', 'svg' => '
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />', 'titulo' => 'Limpeza', 'desc' => 'Limpeza residencial, lavanderia, organização e cuidados com o lar.'],
            ['icon' => 'pc', 'svg' => '
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />', 'titulo' => 'Tecnologia', 'desc' => 'Suporte de TI, configuração de redes, reparo de computadores e consultoria digital.'],
            ['icon' => 'festa', 'svg' => '
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />', 'titulo' => 'Eventos', 'desc' => 'Fotógrafo, DJ, decorador, chef particular e profissionais para festas.'],
            ];
            @endphp
            @foreach($servicosCards as $card)
            <div class="group bg-gray-50 rounded-2xl p-8 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-5 transition-transform duration-300 group-hover:scale-110" style="background-color: #fff5f2;">
                    <svg class="w-7 h-7" style="color: #f4623a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $card['svg'] !!}</svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-3">{{ $card['titulo'] }}</h3>
                <p class="text-base text-gray-500 leading-relaxed">{{ $card['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section id="beneficios" class="py-24" style="background-color: #212529;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
            <p class="text-sm font-semibold uppercase tracking-wider mb-3" style="color: #f4623a;">Benefícios</p>
            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-5">Por que escolher o Alphaview?</h2>
            <p class="text-base text-gray-400 max-w-xl mx-auto leading-relaxed">Oferecemos uma experiência segura, confiável e acessível para todos os condôminos.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @php
            $beneficios = [
            ['svg' => '
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />', 'titulo' => 'Segurança Garantida', 'desc' => 'Verificação de identidade, aprovação administrativa e contratos digitais.'],
            ['svg' => '
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />', 'titulo' => 'Comunidade Real', 'desc' => 'Conecte-se apenas com condôminos verificados da sua residência.'],
            ['svg' => '
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />', 'titulo' => '100% Gratuito', 'desc' => 'Nenhum custo para cadastro, busca ou realização de trocas.'],
            ['svg' => '
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />', 'titulo' => 'Chat Privado', 'desc' => 'Converse diretamente com outros usuários para alinhar detalhes.'],
            ['svg' => '
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />', 'titulo' => 'Contrato Digital', 'desc' => 'Gere contratos em PDF automaticamente após o acordo.'],
            ['svg' => '
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />', 'titulo' => 'Rápido e Fácil', 'desc' => 'Interface intuitiva que permite encontrar e oferecer serviços em minutos.'],
            ];
            @endphp
            @foreach($beneficios as $b)
            <div class="flex gap-4 group">
                <div class="w-14 h-14 rounded-2xl flex-shrink-0 flex items-center justify-center transition-transform duration-300 group-hover:scale-110" style="background-color: rgba(244, 98, 58, 0.15);">
                    <svg class="w-7 h-7" style="color: #f4623a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $b['svg'] !!}</svg>
                </div>
                <div>
                    <h3 class="text-white font-semibold text-lg mb-1">{{ $b['titulo'] }}</h3>
                    <p class="text-base text-gray-400 leading-relaxed">{{ $b['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section id="contato" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
            <p class="text-sm font-semibold uppercase tracking-wider mb-3" style="color: #f4623a;">Contato</p>
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-5">Fale Conosco</h2>
            <p class="text-base text-gray-500 max-w-xl mx-auto leading-relaxed">Envie sua mensagem e retornaremos o mais breve possível.</p>
        </div>
        <div class="max-w-2xl mx-auto">
            <div id="contato-success" class="hidden mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-center">
                <div class="text-2xl mb-2">✓</div>
                <h3 class="text-base font-semibold text-green-900">Mensagem enviada!</h3>
                <p class="mt-1 text-base text-green-700">Entraremos em contato em breve.</p>
            </div>
            <div id="contato-error" class="hidden mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-center">
                <div class="text-2xl mb-2">✕</div>
                <h3 class="text-base font-semibold text-red-900">Erro ao enviar</h3>
                <p class="mt-1 text-base text-red-700" id="contato-error-msg"></p>
            </div>
            <form id="contatoForm" class="bg-gray-50 rounded-2xl p-8 shadow-sm space-y-6">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-base font-medium text-gray-700 mb-2">Nome *</label>
                        <input type="text" name="nome" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#f4623a] focus:border-transparent outline-none transition-all text-base" placeholder="Seu nome">
                    </div>
                    <div>
                        <label class="block text-base font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#f4623a] focus:border-transparent outline-none transition-all text-base" placeholder="seu@email.com">
                    </div>
                </div>
                <div>
                    <label class="block text-base font-medium text-gray-700 mb-2">Assunto *</label>
                    <input type="text" name="assunto" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#f4623a] focus:border-transparent outline-none transition-all text-base" placeholder="Assunto da mensagem">
                </div>
                <div>
                    <label class="block text-base font-medium text-gray-700 mb-2">Mensagem *</label>
                    <textarea name="mensagem" rows="5" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#f4623a] focus:border-transparent outline-none transition-all resize-none text-base" placeholder="Escreva sua mensagem aqui..."></textarea>
                </div>
                <div class="text-center">
                    <button type="submit" id="contatoSubmitBtn" class="inline-flex items-center justify-center px-8 py-3.5 rounded-full text-white font-semibold text-lg transition-all duration-300 hover:shadow-lg hover:scale-105" style="background-color: #f4623a;">
                        Enviar Mensagem
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </div>
            </form>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 mt-16">
                <div class="text-center">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background-color: #fff5f2;">
                        <svg class="w-7 h-7" style="color: #f4623a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 text-base mb-1">Email</h3>
                    <p class="text-base text-gray-500">contato@alphaview.com</p>
                </div>
                <div class="text-center">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background-color: #fff5f2;">
                        <svg class="w-7 h-7" style="color: #f4623a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 text-base mb-1">Localização</h3>
                    <p class="text-base text-gray-500">São Paulo, SP</p>
                </div>
                <div class="text-center">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background-color: #fff5f2;">
                        <svg class="w-7 h-7" style="color: #f4623a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 text-base mb-1">Horário</h3>
                    <p class="text-base text-gray-500">Seg a Sex, 9h às 18h</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-24" style="background-color: #f8f9fa;">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-5">Pronto para começar?</h2>
        <p class="text-lg text-gray-500 mb-10 max-w-2xl mx-auto leading-relaxed">
            Junte-se a centenas de condôminos que já estão trocando serviços e fortalecendo sua comunidade residencial.
        </p>
        <a href="{{ $base }}/cadastrar" target="_blank" class="inline-flex items-center px-10 py-4 rounded-full text-white font-semibold text-lg transition-all duration-300 hover:shadow-2xl hover:scale-105" style="background-color: #f4623a;">
            Crie sua Conta Grátis
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
        </a>
    </div>
</section>

<script nonce="{{ $csp_nonce }}">
    (function() {
        var form = document.getElementById('contatoForm');
        if (!form) return;
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var btn = document.getElementById('contatoSubmitBtn');
            btn.disabled = true;
            btn.textContent = 'Enviando...';
            document.getElementById('contato-error').classList.add('hidden');
            var formData = new FormData(form);
            fetch('{{ $base }}/api/contato', {
                    method: 'POST',
                    headers: {
                        'X-XSRF-TOKEN': (function() {
                            var m = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
                            return m ? decodeURIComponent(m[1]) : '';
                        })(),
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(function(r) {
                    return r.json().then(function(d) {
                        return {
                            status: r.status,
                            data: d
                        };
                    });
                })
                .then(function(result) {
                    if (result.status === 200 || result.status === 201) {
                        form.classList.add('hidden');
                        document.getElementById('contato-error').classList.add('hidden');
                        document.getElementById('contato-success').classList.remove('hidden');
                        setTimeout(function() {
                            form.reset();
                            form.classList.remove('hidden');
                            document.getElementById('contato-success').classList.add('hidden');
                        }, 3000);
                    } else {
                        var errEl = document.getElementById('contato-error');
                        var err_msg = result.data.message || 'Erro ao enviar mensagem.';
                        document.getElementById('contato-error-msg').textContent = err_msg;
                        errEl.classList.remove('hidden');
                    }
                })
                .catch(function() {
                    var errEl = document.getElementById('contato-error');
                    document.getElementById('contato-error-msg').textContent = 'Erro de conexão. Verifique sua internet.';
                    errEl.classList.remove('hidden');
                })
                .finally(function() {
                    btn.disabled = false;
                    btn.textContent = 'Enviar Mensagem';
                });
        });
    })();
</script>

<style nonce="{{ $csp_nonce }}">
    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection