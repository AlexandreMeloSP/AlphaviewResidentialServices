<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redefinir Senha - Alphaview Serviços Residenciais</title>
    <link rel="icon" type="image/x-icon" href="{{ $base }}/favicon.ico">
    @vite(['resources/css/app.css'])
</head>
<body class="antialiased">
    <div class="flex min-h-screen">
        <div class="hidden lg:flex lg:w-1/2 items-center justify-center p-12" style="background-color: #212529;">
            <div class="max-w-md text-center">
                <h1 class="text-4xl sm:text-5xl font-bold leading-tight">
                    <span style="color: #f4623a;">Alphaview</span><br>
                    <span class="text-white">Residence</span><br>
                    <span class="text-white">Services</span>
                </h1>
                <p class="mt-6 text-lg text-gray-400 leading-relaxed">
                    Plataforma de troca de serviços residenciais entre condôminos.
                </p>
            </div>
        </div>

        <div class="flex w-full lg:w-1/2 items-center justify-center p-6 sm:p-12 bg-white">
            <div class="w-full max-w-md space-y-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Redefinir Senha</h2>
                    <p class="mt-2 text-sm text-gray-500">Crie uma nova senha para sua conta.</p>
                </div>

                <div id="error-msg" class="hidden p-3 rounded-lg bg-red-50 text-red-700 text-sm"></div>
                <div id="success-msg" class="hidden p-4 rounded-xl bg-green-50 border border-green-200 text-center">
                    <div class="text-2xl mb-2">✓</div>
                    <h3 class="text-sm font-semibold text-green-900">Senha redefinida!</h3>
                    <p class="mt-1 text-sm text-green-700">Redirecionando para o login...</p>
                </div>

                <form id="resetForm" class="space-y-5">
                    <input type="hidden" id="token" value="{{ $token ?? '' }}">
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">E-mail</label>
                        <input type="email" id="email" name="email" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-900 focus:ring-2 focus:ring-[#f4623a] focus:border-transparent focus:bg-white outline-none transition-all text-base"
                            placeholder="seu@email.com">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Nova Senha</label>
                        <input type="password" id="password" name="password" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-900 focus:ring-2 focus:ring-[#f4623a] focus:border-transparent focus:bg-white outline-none transition-all text-base"
                            placeholder="••••••••">
                        <p class="mt-1 text-xs text-gray-400">Mínimo 8 caracteres com maiúsculas, minúsculas, números e símbolos.</p>
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirmar Nova Senha</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-900 focus:ring-2 focus:ring-[#f4623a] focus:border-transparent focus:bg-white outline-none transition-all text-base"
                            placeholder="••••••••">
                    </div>
                    <button type="submit" id="submitBtn"
                        class="w-full py-3.5 rounded-xl text-white font-semibold text-base transition-all duration-200 hover:shadow-lg"
                        style="background-color: #f4623a;">
                        Redefinir Senha
                    </button>
                </form>

                <p class="text-center text-sm text-gray-500">
                    Lembrou a senha? <a href="{{ $base }}/entrar" class="font-semibold hover:underline" style="color: #f4623a;">Entrar</a>
                </p>
            </div>
        </div>
    </div>

    <script nonce="{{ $csp_nonce }}">
    (function() {
        function getTabId() {
            var tabId = sessionStorage.getItem('alphaview_tab_id');
            if (!tabId) {
                tabId = crypto.randomUUID();
                sessionStorage.setItem('alphaview_tab_id', tabId);
            }
            return tabId;
        }

        fetch('{{ $base }}/sanctum/csrf-cookie', { credentials: 'same-origin' }).then(function() {
            function getXsrfToken() {
                var match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
                return match ? decodeURIComponent(match[1]) : '';
            }

            document.getElementById('resetForm').addEventListener('submit', function(e) {
                e.preventDefault();
                var btn = document.getElementById('submitBtn');
                btn.disabled = true;
                btn.textContent = 'Redefinindo...';
                document.getElementById('error-msg').classList.add('hidden');

                fetch('{{ $base }}/api/auth/reset-password', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-XSRF-TOKEN': getXsrfToken(),
                        'X-TAB-ID': getTabId()
                    },
                    body: JSON.stringify({
                        token: document.getElementById('token').value,
                        email: document.getElementById('email').value,
                        password: document.getElementById('password').value,
                        password_confirmation: document.getElementById('password_confirmation').value
                    })
                })
                .then(function(r) { return r.json().then(function(d) { return { status: r.status, data: d }; }); })
                .then(function(result) {
                    if (result.status === 200) {
                        document.getElementById('resetForm').classList.add('hidden');
                        document.getElementById('success-msg').classList.remove('hidden');
                        setTimeout(function() { window.location.href = '{{ $base }}/entrar'; }, 3000);
                    } else {
                        var el = document.getElementById('error-msg');
                        el.textContent = result.data.message || 'Erro ao redefinir senha.';
                        el.classList.remove('hidden');
                    }
                })
                .catch(function() {
                    var el = document.getElementById('error-msg');
                    el.textContent = 'Erro de conexão.';
                    el.classList.remove('hidden');
                })
                .finally(function() {
                    btn.disabled = false;
                    btn.textContent = 'Redefinir Senha';
                });
            });
        });
    })();
    </script>
</body>
</html>
