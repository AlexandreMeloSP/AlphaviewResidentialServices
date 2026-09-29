# Alphaview Serviços Residenciais

![Status](https://img.shields.io/badge/Status-Ativo-success)
![Disciplina](https://img.shields.io/badge/Disciplina-Projeto_Aplicado-blue)
![Foco](https://img.shields.io/badge/Foco-Seguran%C3%A7a_da_Informa%C3%A7%C3%A3o-red)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

---

## Sobre o Projeto

O **Alphaview** é uma plataforma web que facilita a troca de serviços entre condôminos. A ideia é conectar vizinhos que possuem habilidades diversas como eletricista, encanador, jardineiro, entre outros, permitindo que compartilhem seus talentos de forma segura e colaborativa.

Desenvolvido como projeto aplicado na pós-graduação em **Segurança da Informação e Análise Forense**, o sistema foi projetado com foco em escalabilidade e boas práticas de segurança desde o planejamento até o deploy.

O código foi desenvolvido e submetido a testes de funcionalidade e segurança com auxíio de IA, evitando principalmente, **code smells** e **dark code**, para legibilidade.

Todo projeto foi balizado sob as diretrizes do "Escopo e Elementos Obrigatórios" disponíveis em:

[![Escopo: Obrigatório](https://img.shields.io/badge/Escopo-Elementos_Obribatórios-red)](https://github.com/ziraldocardoso/Projeto_aplicado-praticas_de_mercado/blob/main/Escopo_e_elementos_obrigatorios.md)
[![Git Repo](https://img.shields.io/badge/GitHub-Tutor:_Ziraldo_Cardoso-blue?logo=github)](https://github.com/ziraldocardoso)

---

## Funcionalidades

### Para Usuários

- **Cadastro com verificação**: Criação de conta com validação por e-mail
- **Recuperação de senha**: Possibilidade de recuper a senha por e-mail
- **Aprovação administrativa**: Conta ativada completamente após revisão do administrador
- **Perfil com serviços**: Cadastro de habilidades e serviços oferecidos
- **Sistema de mensagens**: Chat em tempo real entre condôminos
- **Trocas e contratos**: Propostas, acordos e contratos digitais
- **Autenticação em 2 fatores**: MFA opcional via código por e-mail

### Para Administradores

- **Painel de gestão**: Visualização e administração de usuários e serviços
- **Dashboard**: Informação por dashboard e impressão de relatórios
- **Aprovação de cadastros**: Aprovação ou rejeição de novos usuários
- **Relatórios**: Estatísticas de uso da plataforma
- **Accordion expandível**: Detalhes e gerenciamento completos dos usuários

---

## Fluxo de Uso

```
1. Landing Page + Entrar/Cadastro → Verificação de e-mail → Login → Aprovação do admin
2. Login → Navegação nos serviços disponíveis
3. Início de conversa → Negociação → Acordo
4. Geração de contrato digital → Assinatura Digital → Finalização da troca → Impressão PDF
```

---

## Infraestrutura e Segurança

O projeto foi estruturado seguindo as práticas de **desenvolvimento seguro**, com atenção especial às mitigações do **OWASP Top 10:2025**:

- **Server VPS**: SSH access key, root desativado, senhas desativadas, Fail2Ban ativado (4 x 24h), DNS, HTTPS e Log of Audit
- **Controle de acesso**: Usuários só acessam seus próprios dados; rotas protegidas por autenticação e autorização
- **Proteção contra injeção**: Utilização de ORM (Eloquent) com prepared statements e SQL injection protection
- **Headers de segurança**: CSP, HSTS, X-Frame-Options e outras proteções configuradas
- **CSRF**: Token de proteção em todas as requisições sensíveis
- **Rate limiting**: Bloqueio temporário após múltiplas tentativas com falhas
- **Senhas seguras**: Hash com Argon2id + pepper configurável
- **LGPD**: Política de Privacidade e Termos de Uso integrados ao cadastro

---

A configuração **SSL/TLS** do servidor foi submetida ao teste Qualys SSL Labs, e obteve a classificação "A" com suporte a **PQC** ativo. [<sub>ctrl.sytes.net/alphaview</sub>](https://www.ssllabs.com/ssltest/analyze.html?d=ctrl.sytes.net&latest)

![Gráfico SSLLabs](/docs/img/ssl-labs-report.png)
![Gráfico SSLLabs](https://img.shields.io/badge/coverage-95%25-green)

---

## Tecnologias

| Camada             | Tecnologias                                                   |
| ------------------ | ------------------------------------------------------------- |
| **Frontend**       | React 19, TypeScript, Tailwind CSS 4, Vite                    |
| **Backend**        | Laravel 13.8, PHP 8.3 + Dompdf / Browsershot                  |
| **Banco de dados** | MySQL 8.4 + Redis                                             |
| **Deploy**         | Oracle VPS + Dokploy + Docker, Nginx, SSL/TLS (Let's Encrypt) |
| **CI/CD**          | GitHub Actions                                                |

---

## Como Executar Localmente (Docker)

```bash
# Clone o repositório
git clone https://github.com/AlexandreMeloSP/AlphaviewResidentialServices.git
cd AlphaviewResidentialServices

# Instale as dependências
composer install
npm install

# Configure o ambiente
cp .env.example .env
php artisan key:generate

# Execute as migrações e seeders
php artisan migrate --seed

# Inicie o servidor de desenvolvimento
npm run dev
php artisan serve
```

---

## Estrutura do Projeto

```
├── .../                # Lógica do servidor (Models, Controllers, Policies)
├── ...../../           # Componentes React e páginas
├── ...../              # Definição de rotas
├── ...../              # Migrações e seeders
├── ...../              # Configurações de deploy
├── ...../              # Documentação do projeto
└── ...../              # Assets públicos e build
```

<sub>Omitido por segurança.</sub>

---

## Checklist de Entrega

- [x] Aplicação web funcionando e acessível
- [x] Acesso à VPS com uso de chave SSH e Fail2Ban (4 x 24h)
- [x] HTTPS configurado com certificado válido
- [x] Teste SSL Labs com classificação A
- [x] Repositório público no GitHub (segurança 2FA - OTP ativa)
- [x] `.gitignore` configurado sem credenciais expostas
- [x] Login, painel interno e logout funcionais
- [x] Desenvolvimento com auxílio de IA para revisão de segurança (VS Code)
- [x] Documentação das mitigações OWASP Top 10:2025
- [x] Pipeline CI/CD automatizado
- [x] Fluxo completo de cadastro com LGPD
- [x] Verificação de e-mail e aprovação administrativa

---

## Licença

[![Git Repo](https://img.shields.io/badge/GitHub-Repo-blue?logo=github)](https://github.com/AlexandreMeloSP/AlphaviewResidentialServices) [![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
