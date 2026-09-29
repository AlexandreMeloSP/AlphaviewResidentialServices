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

        <div style="padding:32px;text-align:center;">
            <h2 style="color:#212529;font-size:18px;margin:0 0 16px;">Código de Verificação</h2>
            <p style="color:#555;font-size:14px;line-height:1.6;">
                Olá <strong>{{ $user->name }}</strong>,
            </p>
            <p style="color:#555;font-size:14px;line-height:1.6;">
                Use o código abaixo para confirmar seu login:
            </p>

            <div style="margin:32px 0;">
                <span style="display:inline-block;background-color:#212529;color:#ffffff;font-size:32px;font-weight:bold;letter-spacing:8px;padding:16px 32px;border-radius:8px;font-family:monospace;">
                    {{ $code }}
                </span>
            </div>

            <p style="color:#999;font-size:12px;line-height:1.5;">
                Este código expira em 5 minutos. Se você não solicitou este login, ignore este email.
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