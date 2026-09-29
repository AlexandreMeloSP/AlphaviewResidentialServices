<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin:0;padding:0;background-color:#f8f9fa;font-family:sans-serif;">
    <div style="max-width:600px;margin:40px auto;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">

        <div style="background-color:#212529;padding:24px 32px;text-align:center;">
            <h1 style="color:#f4623a;font-size:20px;margin:0;">Alphaview</h1>
            <p style="color:#999;font-size:13px;margin:4px 0 0;">Serviços Residenciais</p>
        </div>

        <div style="padding:32px;">
            <h2 style="color:#212529;font-size:18px;margin:0 0 16px;">Novo usuário aguardando aprovação</h2>
            <p style="color:#555;font-size:14px;line-height:1.6;">
                Um novo usuário completou o cadastro e está aguardando sua aprovação.
            </p>

            <div style="background-color:#f8f9fa;border-radius:8px;padding:16px;margin:24px 0;">
                <p style="color:#212529;font-size:13px;font-weight:600;margin:0 0 8px;">Dados do usuário:</p>
                <p style="color:#555;font-size:13px;line-height:1.8;margin:0;">
                    <strong>Nome:</strong> {{ $user->name }}<br>
                    <strong>E-mail:</strong> {{ $user->email }}<br>
                    <strong>CPF:</strong> {{ substr($user->cpf, 0, 3) }}.{{ substr($user->cpf, 3, 3) }}.{{ substr($user->cpf, 6, 3) }}-{{ substr($user->cpf, 9, 2) }}<br>
                    <strong>Data do cadastro:</strong> {{ $user->created_at->format('d/m/Y H:i') }}
                </p>
            </div>

            <p style="color:#555;font-size:14px;line-height:1.6;">
                Para aprovar ou rejeitar este cadastro, clique em um dos botões abaixo:
            </p>

            <div style="text-align:center;margin:32px 0;">
                <a href="{{ $approveUrl }}"
                   style="display:inline-block;background-color:#28a745;color:#ffffff;text-decoration:none;padding:14px 32px;border-radius:8px;font-weight:600;font-size:14px;margin:0 8px;">
                    Aprovar
                </a>
                <a href="{{ $rejectUrl }}"
                   style="display:inline-block;background-color:#dc3545;color:#ffffff;text-decoration:none;padding:14px 32px;border-radius:8px;font-weight:600;font-size:14px;margin:0 8px;">
                    Rejeitar
                </a>
            </div>

            <p style="color:#999;font-size:12px;line-height:1.5;">
                Você também pode gerenciar este usuário painel administrativo.
            </p>
        </div>

        <div style="background-color:#f8f9fa;padding:16px 32px;text-align:center;">
            <p style="color:#999;font-size:11px;margin:0;">
                © {{ date('Y') }} Alphaview Serviços Residenciais
            </p>
        </div>
    </div>
</body>
</html>
