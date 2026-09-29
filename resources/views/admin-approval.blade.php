<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aprovação de Usuário - Alphaview</title>
    <link rel="icon" type="image/x-icon" href="{{ $base }}/favicon.ico">
    @vite(['resources/css/app.css'])
    <style nonce="{{ $csp_nonce }}">
        .btn-approve {
            background-color: #28a745;
        }

        .btn-approve:hover {
            background-color: #218838;
        }

        .btn-reject {
            background-color: #dc3545;
        }

        .btn-reject:hover {
            background-color: #c82333;
        }

        .btn-login {
            background-color: #f4623a;
        }

        .btn-login:hover {
            background-color: #d94f2a;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            animation: fadeIn 0.4s ease-out;
        }
    </style>
</head>

<body class="antialiased" style="margin:0;padding:0;background-color:#f8f9fa;font-family:sans-serif;">
    <div style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem;">
        <div class="card" style="background:#ffffff;border-radius:12px;max-width:480px;width:100%;box-shadow:0 2px 8px rgba(0,0,0,0.08);overflow:hidden;">

            <div style="background-color:#212529;padding:24px 32px;text-align:center;">
                <h1 style="color:#f4623a;font-size:20px;margin:0;">Alphaview</h1>
                <p style="color:#999;font-size:13px;margin:4px 0 0;">Serviços Residenciais</p>
            </div>

            <div style="padding:32px;text-align:center;">
                @if($error)
                <div style="display:inline-block;background-color:#fee2e2;color:#dc3545;width:60px;height:60px;border-radius:50%;line-height:60px;font-size:28px;margin-bottom:16px;">&#10007;</div>
                <h2 style="color:#212529;font-size:18px;margin:0 0 12px;">Link inválido</h2>
                <p style="color:#555;font-size:14px;line-height:1.6;">{{ $error }}</p>
                @elseif($already)
                <div style="display:inline-block;background-color:#fff3cd;color:#ffc107;width:60px;height:60px;border-radius:50%;line-height:60px;font-size:28px;margin-bottom:16px;">&#9888;</div>
                <h2 style="color:#212529;font-size:18px;margin:0 0 12px;">{{ $already }}</h2>
                <p style="color:#555;font-size:14px;line-height:1.6;">Este usuário já foi processado anteriormente.</p>
                @else
                <div style="display:inline-block;background-color:#e7f1ff;color:#0d6efd;width:60px;height:60px;border-radius:50%;line-height:60px;font-size:28px;margin-bottom:16px;">&#9998;</div>
                <h2 style="color:#212529;font-size:18px;margin:0 0 12px;">Aprovação de Usuário</h2>
                <p style="color:#555;font-size:14px;line-height:1.6;margin-bottom:24px;">
                    Um novo usuário completou o cadastro e está aguardando sua decisão.
                </p>

                <div style="background-color:#f8f9fa;border-radius:8px;padding:16px;margin-bottom:24px;text-align:left;">
                    <p style="color:#212529;font-size:13px;font-weight:600;margin:0 0 8px;">Dados do usuário:</p>
                    <p style="color:#555;font-size:13px;line-height:1.8;margin:0;">
                        <strong>Nome:</strong> {{ $usuario->name }}<br>
                        <strong>E-mail:</strong> {{ $usuario->email }}<br>
                        <strong>CPF:</strong> {{ substr($usuario->cpf, 0, 3) }}.{{ substr($usuario->cpf, 3, 3) }}.{{ substr($usuario->cpf, 6, 3) }}-{{ substr($usuario->cpf, 9, 2) }}<br>
                        <strong>Data:</strong> {{ $usuario->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>

                <p style="color:#999;font-size:12px;margin-bottom:20px;">
                    Para aprovar ou rejeitar, faça login como administrador.
                </p>

                <a href="{{ $base }}/entrar?redirect={{ urlencode('/app/admin/aprovacoes?user_id=' . $usuario->id . '&action=' . $action . '&token=' . $token) }}"
                    class="btn-login"
                    style="display:inline-block;color:#ffffff;text-decoration:none;padding:14px 48px;border-radius:8px;font-weight:600;font-size:14px;width:100%;box-sizing:border-box;">
                    Fazer Login como Admin
                </a>
                @endif
            </div>

            <div style="background-color:#f8f9fa;padding:16px 32px;text-align:center;">
                <p style="color:#999;font-size:11px;margin:0;">
                    © {{ date('Y') }} Alphaview Serviços Residenciais
                </p>
            </div>
        </div>
    </div>
</body>

</html>