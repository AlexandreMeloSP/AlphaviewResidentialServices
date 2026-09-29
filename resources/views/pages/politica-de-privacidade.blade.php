<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Política de Privacidade - Alphaview Serviços Residenciais</title>
    <link rel="icon" type="image/x-icon" href="{{ $base }}/favicon.ico">
    @vite(['resources/css/app.css'])
</head>
<body class="antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
                <a href="{{ $base }}/" class="flex items-center gap-2">
                    <span class="text-2xl font-bold" style="color: #f4623a;">Alphaview</span>
                    <span class="text-2xl font-bold text-gray-900">Serviços Residenciais</span>
                </a>
                <a href="{{ $base }}/entrar" class="text-sm font-medium hover:underline" style="color: #f4623a;">Voltar ao Login</a>
            </div>
        </header>

        <!-- Conteúdo -->
        <main class="flex-1 max-w-4xl mx-auto px-4 py-8 w-full">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Política de Privacidade</h1>
            <p class="text-sm text-gray-500 mb-8">Última atualização: 01 de setembro de 2026</p>

            <div class="prose prose-gray max-w-none space-y-6">
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">1. Introdução</h2>
                    <p class="text-gray-700 leading-relaxed">
                        A Alphaview Serviços Residenciais ("Plataforma") valoriza a privacidade dos seus usuários. Esta Política de Privacidade descreve como coletamos, usamos, armazenamos e protegemos suas informações pessoais, em conformidade com a Lei Geral de Proteção de Dados (LGPD - Lei nº 13.709/2018).
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">2. Dados Coletados</h2>
                    <p class="text-gray-700 leading-relaxed">Coletamos os seguintes dados pessoais durante o cadastro e uso da Plataforma:</p>
                    <ul class="list-disc list-inside text-gray-700 mt-2 space-y-1">
                        <li><strong>Nome completo</strong> — para identificação na Plataforma</li>
                        <li><strong>Endereço de e-mail</strong> — para comunicação e verificação de conta</li>
                        <li><strong>CPF (Cadastro de Pessoa Física)</strong> — para validação de identidade</li>
                        <li><strong>Telefone</strong> — para contato entre usuários (opcional)</li>
                        <li><strong>Biografia e endereço</strong> — para perfil público (opcional)</li>
                        <li><strong>Dados de uso</strong> — informações sobre interações na Plataforma</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">3. Finalidade do Tratamento</h2>
                    <p class="text-gray-700 leading-relaxed">Seus dados são utilizados para:</p>
                    <ul class="list-disc list-inside text-gray-700 mt-2 space-y-1">
                        <li>Viabilizar o cadastro e autenticação de usuários</li>
                        <li>Conectar prestadores de serviços a contratantes</li>
                        <li>Processar e gerenciar solicitações de serviços</li>
                        <li>Enviar notificações sobre sua conta e atividades</li>
                        <li>Garantir a segurança e integridade da Plataforma</li>
                        <li>Cumprir obrigações legais e regulatórias</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">4. Compartilhamento de Dados</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Seus dados pessoais <strong>não são vendidos</strong> a terceiros. O compartilhamento ocorre apenas nas seguintes situações:
                    </p>
                    <ul class="list-disc list-inside text-gray-700 mt-2 space-y-1">
                        <li>Com outros usuários da Plataforma (apenas nome e informações de perfil públicas)</li>
                        <li>Para cumprimento de ordens judiciais ou legais</li>
                        <li>Para proteção dos direitos e segurança da Plataforma e seus usuários</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">5. Armazenamento e Segurança</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Seus dados são armazenados em servidores seguros com criptografia. Utilizamos medidas técnicas e organizacionais para proteger suas informações contra acesso não autorizado, alteração, divulgação ou destruição.
                    </p>
                    <ul class="list-disc list-inside text-gray-700 mt-2 space-y-1">
                        <li>Senhas armazenadas com hash Argon2id + pepper</li>
                        <li>Comunicação criptografada via HTTPS/SSL</li>
                        <li>Acesso restrito a dados pessoais</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">6. Seus Direitos (LGPD)</h2>
                    <p class="text-gray-700 leading-relaxed">Conforme a LGPD, você tem direito a:</p>
                    <ul class="list-disc list-inside text-gray-700 mt-2 space-y-1">
                        <li><strong>Confirmação</strong> da existência de tratamento de dados</li>
                        <li><strong>Acesso</strong> aos seus dados pessoais</li>
                        <li><strong>Correção</strong> de dados incompletos ou desatualizados</li>
                        <li><strong>Anonimização, bloqueio ou eliminação</strong> de dados desnecessários</li>
                        <li><strong>Portabilidade</strong> dos dados</li>
                        <li><strong>Eliminação</strong> dos dados tratados com consentimento</li>
                        <li><strong>Informação</strong> sobre compartilhamento de dados</li>
                        <li><strong>Revogação</strong> do consentimento</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">7. Cookies</h2>
                    <p class="text-gray-700 leading-relaxed">
                        A Plataforma utiliza cookies essenciais para o funcionamento (sessão e autenticação). Não utilizamos cookies de rastreamento ou publicitários.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">8. Retenção de Dados</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Seus dados são mantidos enquanto sua conta estiver ativa. Após a exclusão da conta, os dados são removidos permanentemente em até 30 dias, exceto quando exigido por lei.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">9. Menores de Idade</h2>
                    <p class="text-gray-700 leading-relaxed">
                        A Plataforma não é destinada a menores de 18 anos. Não coletamos intencionalmente dados de menores de idade.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">10. Alterações nesta Política</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Esta Política pode ser atualizada periodicamente. Notificaremos sobre alterações significativas por e-mail ou na Plataforma.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">11. Contato</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Para exercer seus direitos ou esclarecer dúvidas, entre em contato através do e-mail: <a href="mailto:slexer@gmail.com" class="hover:underline" style="color: #f4623a;">slexer@gmail.com</a>
                    </p>
                </section>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t mt-8">
            <div class="max-w-4xl mx-auto px-4 py-6 text-center text-sm text-gray-500">
                &copy; 2026 Alphaview Serviços Residenciais. Todos os direitos reservados.
            </div>
        </footer>
    </div>
</body>
</html>
