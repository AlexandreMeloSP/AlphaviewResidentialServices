<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Entrar - Alphaview Serviços Residenciais</title>
    <link rel="icon" type="image/x-icon" href="{{ $base }}/favicon.ico">
    @vite(['resources/css/app.css'])
</head>
<body class="antialiased">
    <div class="flex min-h-screen">
        <!-- Lado esquerdo -->
        <div class="hidden lg:flex lg:w-1/2 items-center justify-center p-12 relative overflow-hidden" style="background-color: #212529;">
            <div class="relative z-10 max-w-md text-center">
                <h1 class="text-4xl sm:text-5xl font-bold leading-tight">
                    <span style="color: #f4623a;">Alphaview</span><br>
                    <span class="text-white">Serviços</span><br>
                    <span class="text-white">Residenciais</span>
                </h1>
                <p class="mt-6 text-lg text-gray-400 leading-relaxed">
                    Plataforma de troca de serviços residenciais entre condôminos.
                </p>
            </div>
        </div>

        <!-- Lado direito -->
        <div class="flex w-full lg:w-1/2 items-center justify-center p-6 sm:p-12 bg-white">
            <div class="w-full max-w-md space-y-8">
                <!-- Tela de login -->
                <div id="step-login">
                    <h2 class="text-3xl font-bold text-gray-900">Entrar</h2>
                    <p class="mt-2 text-sm text-gray-500">Acesse sua conta para gerenciar seus serviços.</p>

                    <div id="login-error" class="hidden mt-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm"></div>

                    <form id="loginForm" method="POST" class="mt-6 space-y-5">
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">E-mail</label>
                            <input type="email" id="email" name="email" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-900 focus:ring-2 focus:ring-[#f4623a] focus:border-transparent focus:bg-white outline-none transition-all text-base"
                                placeholder="seu@email.com">
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Senha</label>
                            <input type="password" id="password" name="password" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-900 focus:ring-2 focus:ring-[#f4623a] focus:border-transparent focus:bg-white outline-none transition-all text-base"
                                placeholder="••••••••">
                            <div class="flex justify-end mt-1">
                                <a href="{{ $base }}/esqueci-senha" class="text-xs font-semibold hover:underline" style="color: #f4623a;">Esqueceu a senha?</a>
                            </div>
                        </div>
                        <button type="submit" id="submitBtn"
                            class="w-full py-3.5 rounded-xl text-white font-semibold text-base transition-all duration-200 hover:shadow-lg hover:scale-[1.01] active:scale-[0.99]"
                            style="background-color: #f4623a;">
                            Entrar
                        </button>
                    </form>
                    <p class="text-center text-sm text-gray-500 mt-6">
                        Não tem uma conta?
                        <a href="{{ $base }}/cadastrar" class="font-semibold hover:underline" style="color: #f4623a;">Cadastre-se</a>
                    </p>

                    <div class="mt-6 p-4 rounded-xl border border-blue-200 bg-blue-50">
                        <div class="flex items-start gap-3">
                            <div class="text-lg mt-0.5">🔒</div>
                            <div>
                                <p class="text-sm font-semibold text-blue-900">Dica de segurança</p>
                                <p class="text-xs text-blue-700 mt-1">Ative a autenticação em duas etapas (MFA) no seu perfil para proteger sua conta com um código extra via email a cada login.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tela de MFA -->
                <div id="step-mfa" class="hidden">
                    <h2 class="text-3xl font-bold text-gray-900">Verificação em duas etapas</h2>
                    <p class="mt-2 text-sm text-gray-500">Digite o código de 6 dígitos enviado para seu email.</p>

                    <div id="mfa-error" class="hidden mt-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm"></div>

                    <form id="mfaForm" class="mt-6 space-y-5">
                        <div>
                            <label for="mfa-code" class="block text-sm font-semibold text-gray-700 mb-2">Código de verificação</label>
                            <input type="text" id="mfa-code" name="code" required maxlength="6"
                                pattern="[0-9]{6}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-900 text-center text-2xl tracking-[0.5em] font-mono focus:ring-2 focus:ring-[#f4623a] focus:border-transparent focus:bg-white outline-none transition-all"
                                placeholder="000000">
                            <p class="mt-2 text-xs text-gray-400 text-center">O código expira em 5 minutos.</p>
                        </div>
                        <button type="submit" id="mfaSubmitBtn"
                            class="w-full py-3.5 rounded-xl text-white font-semibold text-base transition-all duration-200 hover:shadow-lg"
                            style="background-color: #f4623a;">
                            Verificar Código
                        </button>
                        <button type="button" id="mfaBackBtn"
                            class="w-full py-2.5 text-sm text-gray-500 hover:text-gray-700">
                            ← Voltar ao login
                        </button>
                    </form>
                </div>

                <!-- Tela de email não verificado -->
                <div id="step-not-verified" class="hidden">
                    <h2 class="text-3xl font-bold text-gray-900">Email não verificado</h2>
                    <p class="mt-2 text-sm text-gray-500">Verifique sua caixa de entrada e clique no link de confirmação.</p>

                    <div class="mt-6 p-4 rounded-xl bg-amber-50 border border-amber-200">
                        <p class="text-sm text-amber-800" id="verify-msg"></p>
                    </div>

                    <button type="button" id="resendBtn"
                        class="mt-4 w-full py-3 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-all">
                        Reenviar email de verificação
                    </button>
                    <div id="resend-success" class="hidden mt-3 p-3 rounded-lg bg-green-50 text-green-700 text-sm text-center"></div>
                </div>
            </div>
        </div>
    </div>

    <script nonce="{{ $csp_nonce }}">
    (function() {
        var currentEmail = '';

        function getXsrfToken() {
            var match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
            return match ? decodeURIComponent(match[1]) : '';
        }

        function getTabId() {
            var tabId = sessionStorage.getItem('alphaview_tab_id');
            if (!tabId) {
                tabId = crypto.randomUUID();
                sessionStorage.setItem('alphaview_tab_id', tabId);
            }
            return tabId;
        }

        function showError(id, msg) {
            var el = document.getElementById(id);
            el.textContent = msg;
            el.classList.remove('hidden');
        }
        function hideError(id) {
            document.getElementById(id).classList.add('hidden');
        }

        // Fetch CSRF cookie
        fetch('{{ $base }}/sanctum/csrf-cookie', { credentials: 'same-origin' }).then(function() {

            // === LOGIN ===
            document.getElementById('loginForm').addEventListener('submit', function(e) {
                e.preventDefault();
                hideError('login-error');
                var btn = document.getElementById('submitBtn');
                btn.disabled = true;
                btn.textContent = 'Entrando...';

                currentEmail = document.getElementById('email').value;
                var password = document.getElementById('password').value;

                fetch('{{ $base }}/api/auth/login', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-XSRF-TOKEN': getXsrfToken(),
                        'X-TAB-ID': getTabId()
                    },
                    body: JSON.stringify({ email: currentEmail, password: password })
                })
                .then(function(r) { return r.json().then(function(d) { return { status: r.status, data: d }; }); })
                .then(function(result) {
                    var d = result.data;

                    if (d.user) {
                        if (d.tab_id) {
                            sessionStorage.setItem('alphaview_tab_id', d.tab_id);
                        }
                        // Login OK
                        window.location.href = '{{ $base }}/app/dashboard';
                        return;
                    }

                    if (d.requires_mfa) {
                        // MFA necessário
                        document.getElementById('step-login').classList.add('hidden');
                        document.getElementById('step-mfa').classList.remove('hidden');
                        document.getElementById('mfa-code').focus();
                        return;
                    }

                    if (d.requires_verification) {
                        document.getElementById('step-login').classList.add('hidden');
                        document.getElementById('step-not-verified').classList.remove('hidden');
                        document.getElementById('verify-msg').textContent = d.message;
                        return;
                    }

                    showError('login-error', d.message || 'Erro ao fazer login.');
                })
                .catch(function() {
                    showError('login-error', 'Erro de conexão. Verifique sua internet.');
                })
                .finally(function() {
                    btn.disabled = false;
                    btn.textContent = 'Entrar';
                });
            });

            // === MFA ===
            document.getElementById('mfaForm').addEventListener('submit', function(e) {
                e.preventDefault();
                hideError('mfa-error');
                var btn = document.getElementById('mfaSubmitBtn');
                btn.disabled = true;
                btn.textContent = 'Verificando...';

                var code = document.getElementById('mfa-code').value;

                fetch('{{ $base }}/api/auth/verify-mfa', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-XSRF-TOKEN': getXsrfToken(),
                        'X-TAB-ID': getTabId()
                    },
                    body: JSON.stringify({ code: code })
                })
                .then(function(r) { return r.json().then(function(d) { return { status: r.status, data: d }; }); })
                .then(function(result) {
                    if (result.data.user) {
                        if (result.data.tab_id) {
                            sessionStorage.setItem('alphaview_tab_id', result.data.tab_id);
                        }
                        window.location.href = '{{ $base }}/app/dashboard';
                    } else {
                        showError('mfa-error', result.data.message || 'Código inválido.');
                    }
                })
                .catch(function() {
                    showError('mfa-error', 'Erro de conexão.');
                })
                .finally(function() {
                    btn.disabled = false;
                    btn.textContent = 'Verificar Código';
                });
            });

            document.getElementById('mfaBackBtn').addEventListener('click', function() {
                document.getElementById('step-mfa').classList.add('hidden');
                document.getElementById('step-login').classList.remove('hidden');
                hideError('mfa-error');
            });

            // === REENVIAR VERIFICAÇÃO ===
            document.getElementById('resendBtn').addEventListener('click', function() {
                var btn = this;
                btn.disabled = true;
                btn.textContent = 'Enviando...';

                fetch('{{ $base }}/api/auth/resend-verification', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-XSRF-TOKEN': getXsrfToken(),
                        'X-TAB-ID': getTabId()
                    },
                    body: JSON.stringify({ email: currentEmail })
                })
                .then(function(r) { return r.json(); })
                .then(function(d) {
                    var el = document.getElementById('resend-success');
                    el.textContent = d.message;
                    el.classList.remove('hidden');
                    setTimeout(function() { el.classList.add('hidden'); }, 5000);
                })
                .finally(function() {
                    btn.disabled = false;
                    btn.textContent = 'Reenviar email de verificação';
                });
            });
        });
    })();
    </script>
</body>
</html>
