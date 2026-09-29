<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificação de Email - Alphaview</title>
    <link rel="icon" type="image/x-icon" href="{{ $base }}/favicon.ico">
    <style nonce="{{ $csp_nonce }}">
        body { font-family: sans-serif; background: #f8f9fa; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: #fff; border-radius: 12px; padding: 40px; max-width: 480px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .icon { font-size: 64px; margin-bottom: 16px; }
        .success .icon { color: #198754; }
        .error .icon { color: #dc3545; }
        h2 { color: #212529; margin-bottom: 12px; }
        p { color: #555; line-height: 1.6; }
        a { display: inline-block; margin-top: 20px; padding: 12px 32px; background: #f4623a; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600; }
        a:hover { background: #d94f2a; }
    </style>
</head>
<body>
    <div class="card {{ $success ? 'success' : 'error' }}">
        <div class="icon">{!! $success ? '&#10004;' : '&#10006;' !!}</div>
        <h2>{{ $success ? 'Email verificado!' : 'Erro na verificação' }}</h2>
        <p>{{ $message }}</p>
        <a href="{{ $base }}/entrar">Ir para o Login</a>
    </div>
</body>
</html>
