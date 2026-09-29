<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Alphaview Serviços Residenciais - Plataforma de troca de servicos entre condôminos">
    <link rel="icon" type="image/x-icon" href="{{ $base }}/favicon.ico">
    <title>@yield('title', 'Alphaview Serviços Residenciais')</title>
    @vite(['resources/css/app.css'])
</head>
<body class="antialiased">
    @include('components.navbar')

    <main>
        @yield('content')
    </main>

    @include('components.footer')
    <script nonce="{{ $csp_nonce }}">
        fetch('{{ $base }}/sanctum/csrf-cookie', { credentials: 'same-origin' });
        document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
            anchor.addEventListener('click', function(e) {
                var id = this.getAttribute('href');
                if (id === '#') return;
                var target = document.querySelector(id);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
</body>
</html>
