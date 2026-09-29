@php $p = $base; @endphp
<nav id="navbar" class="fixed top-0 left-0 w-full z-50 transition-all duration-300 bg-transparent">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 md:h-20">
            <a href="{{ $p }}/" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-bold text-sm" style="background-color: #f4623a;">AV</div>
                <span class="text-lg font-bold text-white transition-colors duration-300" id="nav-logo-text">Alphaview</span>
            </a>

            <div class="hidden md:flex items-center gap-8">
                <a href="#inicio" class="nav-link text-sm font-medium transition-colors duration-300 text-white/80 hover:text-white scroll-smooth">Início</a>
                <a href="#sobre" class="nav-link text-sm font-medium transition-colors duration-300 text-white/80 hover:text-white scroll-smooth">Sobre</a>
                <a href="#como-funciona" class="nav-link text-sm font-medium transition-colors duration-300 text-white/80 hover:text-white scroll-smooth">Como Funciona</a>
                <a href="#estatisticas" class="nav-link text-sm font-medium transition-colors duration-300 text-white/80 hover:text-white scroll-smooth">Estatísticas</a>
                <a href="#servicos" class="nav-link text-sm font-medium transition-colors duration-300 text-white/80 hover:text-white scroll-smooth">Serviços</a>
                <a href="#beneficios" class="nav-link text-sm font-medium transition-colors duration-300 text-white/80 hover:text-white scroll-smooth">Benefícios</a>
                <a href="#contato" class="nav-link text-sm font-medium transition-colors duration-300 text-white/80 hover:text-white scroll-smooth">Contato</a>
            </div>

            <div class="hidden md:flex items-center gap-4">
                <a href="{{ $p }}/entrar" target="_blank" class="nav-link text-sm font-medium transition-colors duration-300 text-white/80 hover:text-white">Entrar</a>
                <a href="{{ $p }}/cadastrar" target="_blank" class="inline-flex items-center px-5 py-2.5 rounded-full text-sm font-semibold text-white transition-all duration-300 hover:shadow-lg hover:scale-105" style="background-color: #f4623a;">Cadastrar</a>
            </div>

            <button id="mobile-menu-btn" class="md:hidden text-white p-2" aria-label="Menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    <div id="mobile-menu" class="hidden md:hidden bg-white/95 backdrop-blur-sm border-t border-gray-100">
        <div class="px-4 py-4 space-y-2">
            <a href="#inicio" class="block py-2 text-sm font-medium text-gray-700 hover:text-[#f4623a] mobile-nav-link">Início</a>
            <a href="#sobre" class="block py-2 text-sm font-medium text-gray-700 hover:text-[#f4623a] mobile-nav-link">Sobre</a>
            <a href="#como-funciona" class="block py-2 text-sm font-medium text-gray-700 hover:text-[#f4623a] mobile-nav-link">Como Funciona</a>
            <a href="#estatisticas" class="block py-2 text-sm font-medium text-gray-700 hover:text-[#f4623a] mobile-nav-link">Estatísticas</a>
            <a href="#servicos" class="block py-2 text-sm font-medium text-gray-700 hover:text-[#f4623a] mobile-nav-link">Serviços</a>
            <a href="#beneficios" class="block py-2 text-sm font-medium text-gray-700 hover:text-[#f4623a] mobile-nav-link">Benefícios</a>
            <a href="#contato" class="block py-2 text-sm font-medium text-gray-700 hover:text-[#f4623a] mobile-nav-link">Contato</a>
            <hr class="my-2 border-gray-200">
            <a href="{{ $p }}/entrar" target="_blank" class="block py-2 text-sm font-medium text-gray-700 hover:text-[#f4623a]">Entrar</a>
            <a href="{{ $p }}/cadastrar" target="_blank" class="block py-2.5 text-center text-sm font-semibold text-white rounded-full" style="background-color: #f4623a;">Cadastrar</a>
        </div>
    </div>
</nav>

<script nonce="{{ $csp_nonce }}">
    (function() {
        const navbar = document.getElementById('navbar');
        const logoText = document.getElementById('nav-logo-text');
        const navLinks = document.querySelectorAll('.nav-link');
        const mobileBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        function updateNavbar() {
            if (window.scrollY > 50) {
                navbar.classList.remove('bg-transparent');
                navbar.classList.add('bg-white', 'shadow-md');
                if (logoText) logoText.classList.remove('text-white');
                if (logoText) logoText.classList.add('text-gray-900');
                navLinks.forEach(function(link) {
                    link.classList.remove('text-white/80');
                    link.classList.add('text-gray-600');
                    link.classList.remove('hover:text-white');
                    link.classList.add('hover:text-[#f4623a]');
                });
            } else {
                navbar.classList.add('bg-transparent');
                navbar.classList.remove('bg-white', 'shadow-md');
                if (logoText) logoText.classList.add('text-white');
                if (logoText) logoText.classList.remove('text-gray-900');
                navLinks.forEach(function(link) {
                    link.classList.add('text-white/80');
                    link.classList.remove('text-gray-600');
                    link.classList.add('hover:text-white');
                    link.classList.remove('hover:text-[#f4623a]');
                });
            }
        }

        window.addEventListener('scroll', updateNavbar);
        updateNavbar();

        if (mobileBtn && mobileMenu) {
            mobileBtn.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        }

        document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
            anchor.addEventListener('click', function(e) {
                var target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    if (mobileMenu) mobileMenu.classList.add('hidden');
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    })();
</script>
