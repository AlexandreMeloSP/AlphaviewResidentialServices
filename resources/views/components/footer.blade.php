@php $p = $base; @endphp
<footer class="bg-gray-900 text-gray-400">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-bold text-sm" style="background-color: #f4623a;">AV</div>
                    <span class="text-lg font-bold text-white">Alphaview</span>
                </div>
                <p class="text-sm leading-relaxed">
                    Plataforma de troca de servicos entre condôminos.
                    Conectando pessoas e facilitando a vida em comunidade.
                </p>
            </div>

            <div>
                <h3 class="text-white font-semibold mb-4">Links Uteis</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ $p }}/#como-funciona" class="hover:text-white transition-colors">Como Funciona</a></li>
                    <li><a href="{{ $p }}/#servicos" class="hover:text-white transition-colors">Servicos</a></li>
                    <li><a href="{{ $p }}/sobre" class="hover:text-white transition-colors">Sobre</a></li>
                    <li><a href="{{ $p }}/contatos" class="hover:text-white transition-colors">Contato</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-semibold mb-4">Contato</h3>
                <ul class="space-y-2 text-sm">
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>contato@alphaview.com</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Sao Paulo, SP - Brasil</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-800 mt-8 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm">
                &copy; {{ date('Y') }} Alphaview Serviços Residenciais. Todos os direitos reservados.
            </p>
            <p class="text-xs text-gray-500">
                Desenvolvido com dedicacao por Alexandre &mdash; Alphaview Serviços Residenciais
            </p>
        </div>
    </div>
</footer>