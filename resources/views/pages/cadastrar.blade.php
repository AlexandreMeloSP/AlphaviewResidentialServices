<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastrar - Alphaview Serviços Residenciais</title>
    <link rel="icon" type="image/x-icon" href="{{ $base }}/favicon.ico">
    @vite(['resources/css/app.css'])
    <style nonce="{{ $csp_nonce }}">
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 50; align-items: center; justify-content: center; }
        .modal-overlay.active { display: flex; }
        .modal-content { background: white; border-radius: 1rem; max-width: 600px; width: 90%; max-height: 80vh; display: flex; flex-direction: column; }
        .modal-scroll { overflow-y: auto; padding: 2rem; flex: 1; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 1rem 2rem; border-bottom: 1px solid #e5e7eb; }
        .modal-close { cursor: pointer; font-size: 1.5rem; color: #6b7280; background: none; border: none; }
        .modal-close:hover { color: #111827; }
        .step-circle { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.875rem; flex-shrink: 0; }
        @keyframes modalFadeIn { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
        .modal-overlay.active .modal-content { animation: modalFadeIn 0.3s ease-out; }
    </style>
</head>
<body class="antialiased">
    <!-- Modal Politica de Privacidade -->
    <div id="modal-privacidade" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-lg font-bold text-gray-900">Politica de Privacidade</h3>
                <button class="modal-close" onclick="closeModal('modal-privacidade')">&times;</button>
            </div>
            <div class="modal-scroll">
                <div class="prose prose-sm max-w-none text-gray-700 space-y-4">
                    <p><strong>Ultima atualizacao:</strong> 01 de setembro de 2026</p>
                    <h4 class="font-semibold text-gray-900">1. Introducao</h4>
                    <p>A Alphaview Serviços Residenciais ("Plataforma") valoriza a privacidade dos seus usuarios. Esta Politica de Privacidade descreve como coletamos, usamos, armazenamos e protegemos suas informacoes pessoais, em conformidade com a Lei Geral de Protecao de Dados (LGPD).</p>
                    <h4 class="font-semibold text-gray-900">2. Dados Coletados</h4>
                    <p>Coletamos os seguintes dados pessoais durante o cadastro:</p>
                    <ul class="list-disc list-inside"><li>Nome completo</li><li>Endereco de e-mail</li><li>CPF (Cadastro de Pessoa Fisica)</li><li>Telefone e endereco (opcional)</li></ul>
                    <h4 class="font-semibold text-gray-900">3. Finalidade</h4>
                    <p>Seus dados sao utilizados para: cadastro e autenticacao, conexao entre usuarios, processamento de servicos, notificacoes e seguranca da Plataforma.</p>
                    <h4 class="font-semibold text-gray-900">4. Compartilhamento</h4>
                    <p>Seus dados nao sao vendidos a terceiros. O compartilhamento ocorre apenas com outros usuarios (nome e perfil publicos), para cumprimento de ordens legais, e para protecao da Plataforma.</p>
                    <h4 class="font-semibold text-gray-900">5. Seguranca</h4>
                    <p>Dados armazenados com criptografia. Senhas com hash Argon2id. Comunicacao via HTTPS/SSL.</p>
                    <h4 class="font-semibold text-gray-900">6. Seus Direitos (LGPD)</h4>
                    <p>Voce tem direito a: confirmacao, acesso, correcao, anonimizacao, bloqueio, eliminacao, portabilidade e revogacao do consentimento.</p>
                    <h4 class="font-semibold text-gray-900">7. Contato</h4>
                    <p>Para exercer seus direitos: <a href="mailto:slexer@gmail.com" style="color: #f4623a;">slexer@gmail.com</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Termos de Uso -->
    <div id="modal-termos" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-lg font-bold text-gray-900">Termos de Uso</h3>
                <button class="modal-close" onclick="closeModal('modal-termos')">&times;</button>
            </div>
            <div class="modal-scroll">
                <div class="prose prose-sm max-w-none text-gray-700 space-y-4">
                    <p><strong>Ultima atualizacao:</strong> 01 de setembro de 2026</p>
                    <h4 class="font-semibold text-gray-900">1. Aceitacao</h4>
                    <p>Ao acessar a Plataforma, voce concorda com estes Termos de Uso.</p>
                    <h4 class="font-semibold text-gray-900">2. Servico</h4>
                    <p>A Plataforma conecta condominos para troca e prestacao de servicos residenciais.</p>
                    <h4 class="font-semibold text-gray-900">3. Cadastro</h4>
                    <ul class="list-disc list-inside"><li>Minimo 18 anos</li><li>Informacoes verdadeiras</li><li>Uma conta por CPF</li><li>Cadastro sujeito a aprovacao do administrador</li></ul>
                    <h4 class="font-semibold text-gray-900">4. Regras de Conduta</h4>
                    <ul class="list-disc list-inside"><li>Nao usar para fins ilegais</li><li>Nao impersonar outras pessoas</li><li>Nao interferir no funcionamento</li><li>Reportar violacoes</li></ul>
                    <h4 class="font-semibold text-gray-900">5. Servicos</h4>
                    <p>A Plataforma atua como intermediadora. Valores sao definidos pelos usuarios. Recomenda-se formalizar contratos.</p>
                    <h4 class="font-semibold text-gray-900">6. Responsabilidade</h4>
                    <p>A Plataforma e fornecida "como esta". Nao nos responsabilizamos por danos decorrentes do uso ou transacoes entre usuarios.</p>
                    <h4 class="font-semibold text-gray-900">7. Foro</h4>
                    <p>Foro da Comarca de Sao Paulo/SP.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Sucesso Cadastro -->
    <div id="modal-success" class="modal-overlay">
        <div class="modal-content" style="max-width: 480px;">
            <div class="p-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-4" style="background-color: #d1fae5;">
                    <span class="text-3xl" style="color: #198754;">&#10003;</span>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Conta criada com sucesso!</h2>
                <p class="text-sm text-gray-500 mb-6">Um e-mail de verificação foi enviado para o seu endereço. Clique no link para confirmar seu e-mail.</p>

                <div class="p-4 rounded-xl border border-gray-200 bg-gray-50 mb-6 text-left">
                    <p class="text-sm font-bold text-gray-900 mb-2">Próximos passos:</p>
                    <ol class="text-xs text-gray-600 space-y-1.5 list-decimal list-inside">
                        <li>Confirme seu e-mail pelo link enviado</li>
                        <li>Aguarde a aprovação do administrador</li>
                        <li>Você receberá um e-mail quando sua conta for aprovada</li>
                        <li>Acesse a plataforma e comece a utilizar os serviços</li>
                    </ol>
                </div>

                <a href="{{ $base }}/entrar"
                   class="inline-block w-full py-3 rounded-xl text-white font-semibold text-sm transition-all duration-200 hover:shadow-lg hover:scale-[1.01] active:scale-[0.99]"
                   style="background-color: #f4623a;">
                    Ir para o Login
                </a>
            </div>
        </div>
    </div>

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
                    Plataforma de troca de servicos residenciais entre condominos.
                </p>
            </div>
        </div>

        <!-- Lado direito -->
        <div class="flex w-full lg:w-1/2 items-center justify-center p-6 sm:p-12 bg-white">
            <div class="w-full max-w-md space-y-6">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Criar conta</h2>
                    <p class="mt-2 text-sm text-gray-500">Preencha seus dados para comecar.</p>
                </div>

                <div id="register-error" class="hidden p-3 rounded-lg bg-red-50 text-red-700 text-sm"></div>
                <div id="register-success" class="hidden p-3 rounded-lg bg-green-50 text-green-700 text-sm"></div>

                <form id="registerForm" class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nome completo</label>
                        <input type="text" id="name" name="name" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-900 focus:ring-2 focus:ring-[#f4623a] focus:border-transparent focus:bg-white outline-none transition-all text-base"
                            placeholder="Seu nome completo">
                    </div>

                    <div>
                        <label for="cpf" class="block text-sm font-semibold text-gray-700 mb-1">CPF</label>
                        <input type="text" id="cpf" name="cpf" required maxlength="14"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-900 focus:ring-2 focus:ring-[#f4623a] focus:border-transparent focus:bg-white outline-none transition-all text-base"
                            placeholder="000.000.000-00"
                            oninput="this.value = this.value.replace(/[^\d]/g, '').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2')">
                    </div>

                    <div>
                        <label for="reg-email" class="block text-sm font-semibold text-gray-700 mb-1">E-mail</label>
                        <input type="email" id="reg-email" name="email" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-900 focus:ring-2 focus:ring-[#f4623a] focus:border-transparent focus:bg-white outline-none transition-all text-base"
                            placeholder="seu@email.com">
                    </div>

                    <div>
                        <label for="reg-password" class="block text-sm font-semibold text-gray-700 mb-1">Senha</label>
                        <input type="password" id="reg-password" name="password" required minlength="8" autocomplete="new-password"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-900 focus:ring-2 focus:ring-[#f4623a] focus:border-transparent focus:bg-white outline-none transition-all text-base"
                            placeholder="Minimo 8 caracteres">
                    </div>

                    <div>
                        <label for="reg-password-confirm" class="block text-sm font-semibold text-gray-700 mb-1">Confirmar senha</label>
                        <input type="password" id="reg-password-confirm" name="password_confirmation" required autocomplete="new-password"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-900 focus:ring-2 focus:ring-[#f4623a] focus:border-transparent focus:bg-white outline-none transition-all text-base"
                            placeholder="Confirme sua senha">
                    </div>

                    <!-- Checkbox de Termos -->
                    <div class="flex items-start gap-3">
                        <input type="checkbox" id="accepted_terms" name="accepted_terms" required
                            class="mt-1 h-4 w-4 rounded border-gray-300 text-[#f4623a] focus:ring-[#f4623a]">
                        <label for="accepted_terms" class="text-sm text-gray-600">
                            Li e aceito a
                            <a href="javascript:void(0)" onclick="openModal('modal-privacidade')" class="font-semibold hover:underline" style="color: #f4623a;">Politica de Privacidade</a>
                            e os
                            <a href="javascript:void(0)" onclick="openModal('modal-termos')" class="font-semibold hover:underline" style="color: #f4623a;">Termos de Uso</a>.
                        </label>
                    </div>

                    <button type="submit" id="submitBtn" disabled
                        class="w-full py-3.5 rounded-xl text-white font-semibold text-base transition-all duration-200 hover:shadow-lg hover:scale-[1.01] active:scale-[0.99] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                        style="background-color: #f4623a;">
                        Criar Conta
                    </button>
                </form>

                <p class="text-center text-sm text-gray-500">
                    Ja tem uma conta?
                    <a href="{{ $base }}/entrar" class="font-semibold hover:underline" style="color: #f4623a;">Entrar</a>
                </p>

                <!-- Fluxo de cadastro -->
                <div class="p-4 rounded-xl border border-gray-200 bg-gray-50">
                    <h3 class="text-sm font-bold text-gray-900 mb-3">Como funciona o cadastro?</h3>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                        <div class="flex flex-col items-center text-center gap-2">
                            <div class="step-circle text-white" style="background-color: #f4623a;">1</div>
                            <div>
                                <p class="text-xs font-semibold text-gray-900">Crie sua conta</p>
                                <p class="text-[10px] text-gray-500">Preencha seus dados e aceite os termos</p>
                            </div>
                        </div>
                        <div class="flex flex-col items-center text-center gap-2">
                            <div class="step-circle text-white" style="background-color: #f4623a;">2</div>
                            <div>
                                <p class="text-xs font-semibold text-gray-900">Verifique seu e-mail</p>
                                <p class="text-[10px] text-gray-500">Clique no link de confirmacao</p>
                            </div>
                        </div>
                        <div class="flex flex-col items-center text-center gap-2">
                            <div class="step-circle text-white" style="background-color: #f4623a;">3</div>
                            <div>
                                <p class="text-xs font-semibold text-gray-900">Aguarde a aprovacao</p>
                                <p class="text-[10px] text-gray-500">O administrador ira aprovar</p>
                            </div>
                        </div>
                        <div class="flex flex-col items-center text-center gap-2">
                            <div class="step-circle text-white" style="background-color: #f4623a;">4</div>
                            <div>
                                <p class="text-xs font-semibold text-gray-900">Acesse e use</p>
                                <p class="text-[10px] text-gray-500">Utilize os servicos da plataforma</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script nonce="{{ $csp_nonce }}">
    function openModal(id) {
        document.getElementById(id).classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
        document.body.style.overflow = '';
    }
    document.querySelectorAll('.modal-overlay').forEach(function(modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal(modal.id);
        });
    });

    (function() {
        var checkbox = document.getElementById('accepted_terms');
        var submitBtn = document.getElementById('submitBtn');
        checkbox.addEventListener('change', function() {
            submitBtn.disabled = !this.checked;
        });

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

        fetch('{{ $base }}/sanctum/csrf-cookie', { credentials: 'same-origin' }).then(function() {

            document.getElementById('registerForm').addEventListener('submit', function(e) {
                e.preventDefault();
                var errorDiv = document.getElementById('register-error');
                var successDiv = document.getElementById('register-success');
                var submitBtn = document.getElementById('submitBtn');
                errorDiv.classList.add('hidden');
                successDiv.classList.add('hidden');
                submitBtn.disabled = true;
                submitBtn.textContent = 'Cadastrando...';

                var data = {
                    name: document.getElementById('name').value,
                    cpf: document.getElementById('cpf').value,
                    email: document.getElementById('reg-email').value,
                    password: document.getElementById('reg-password').value,
                    password_confirmation: document.getElementById('reg-password-confirm').value,
                    accepted_terms: document.getElementById('accepted_terms').checked ? '1' : '0'
                };

                fetch('{{ $base }}/api/auth/register', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-XSRF-TOKEN': getXsrfToken(),
                        'X-TAB-ID': getTabId()
                    },
                    body: JSON.stringify(data)
                })
                .then(function(response) {
                    return response.json().then(function(body) {
                        return { ok: response.ok, status: response.status, body: body };
                    });
                })
                .then(function(result) {
                    if (result.ok && result.body.message) {
                        document.getElementById('modal-success').classList.add('active');
                        document.body.style.overflow = 'hidden';
                        submitBtn.disabled = true;
                        submitBtn.textContent = 'Cadastrando...';
                        document.getElementById('registerForm').querySelectorAll('input, button').forEach(function(el) { el.disabled = true; });
                    } else if (result.body.errors) {
                        var msg = [...new Set(Object.values(result.body.errors).flat())].join('. ');
                        errorDiv.textContent = msg;
                        errorDiv.classList.remove('hidden');
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Criar Conta';
                    } else {
                        errorDiv.textContent = result.body.message || 'Erro ao cadastrar.';
                        errorDiv.classList.remove('hidden');
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Criar Conta';
                    }
                })
                .catch(function() {
                    errorDiv.textContent = 'Erro de conexão.';
                    errorDiv.classList.remove('hidden');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Criar Conta';
                });
            });
        });
    })();
    </script>
</body>
</html>
