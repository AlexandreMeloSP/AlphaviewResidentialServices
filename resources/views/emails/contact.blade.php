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
            <h2 style="color:#212529;font-size:18px;margin:0 0 16px;">Nova mensagem de contato</h2>

            <table style="width:100%;margin-bottom:20px;">
                <tr>
                    <td style="padding:8px 0;color:#999;font-size:13px;width:100px;">Nome:</td>
                    <td style="padding:8px 0;color:#333;font-size:14px;font-weight:600;">{{ $nome }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0;color:#999;font-size:13px;">Email:</td>
                    <td style="padding:8px 0;color:#333;font-size:14px;font-weight:600;">{{ $email }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0;color:#999;font-size:13px;">Assunto:</td>
                    <td style="padding:8px 0;color:#333;font-size:14px;font-weight:600;">{{ $assunto }}</td>
                </tr>
            </table>

            <div style="background-color:#f8f9fa;border-radius:8px;padding:16px;margin-bottom:20px;">
                <p style="color:#555;font-size:14px;line-height:1.6;margin:0;white-space:pre-wrap;">{{ $mensagem }}</p>
            </div>

            <p style="color:#999;font-size:12px;text-align:center;margin:0;">
                Esta mensagem foi enviada através do formulário de contato da landing page.
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