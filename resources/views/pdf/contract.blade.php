<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrato de Troca de Serviços</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #333;
            padding: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #f4623a;
            padding-bottom: 20px;
        }

        .header h1 {
            font-size: 20pt;
            color: #212529;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 14pt;
            color: #666;
            font-weight: normal;
        }

        .section {
            margin-bottom: 25px;
        }

        .section h3 {
            font-size: 13pt;
            color: #212529;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            min-width: 200px;
            color: #555;
        }

        .info-value {
            color: #333;
        }

        .services-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .services-table th,
        .services-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .services-table th {
            background-color: #f8f9fa;
            color: #212529;
            font-weight: bold;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
        }

        .signature-block {
            width: 45%;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 80px;
            padding-top: 10px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10pt;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>ALPHAVIEW SERVIÇOS RESIDENCIAIS</h1>
        <h2>Contrato de Troca de Serviços</h2>
    </div>

    <div class="section">
        <h3>Dados do Contrato</h3>
        <div class="info-row">
            <span class="info-label">Número do Contrato:</span>
            <span class="info-value">#{{ $contrato->id }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Data de Geração:</span>
            <span class="info-value">{{ $dataGeracao }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Status:</span>
            <span class="info-value">{{ ucfirst($contrato->status) }}</span>
        </div>
    </div>

    <div class="section">
        <h3>Partes Envolvidas</h3>
        <div class="info-row">
            <span class="info-label">Proponente:</span>
            <span class="info-value">{{ $user1->name }} ({{ $user1->email }})</span>
        </div>
        <div class="info-row">
            <span class="info-label">CPF:</span>
            <span class="info-value">{{ $user1->cpf ?? 'Não informado' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Receptor:</span>
            <span class="info-value">{{ $user2->name }} ({{ $user2->email }})</span>
        </div>
        <div class="info-row">
            <span class="info-label">CPF:</span>
            <span class="info-value">{{ $user2->cpf ?? 'Não informado' }}</span>
        </div>
    </div>

    <div class="section">
        <h3>Serviços em Troca</h3>
        <table class="services-table">
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Serviço do Proponente</th>
                    <th>Serviço do Receptor</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Título</strong></td>
                    <td>{{ $serviceProponente->titulo }}</td>
                    <td>{{ $serviceReceptor->titulo }}</td>
                </tr>
                <tr>
                    <td><strong>Categoria</strong></td>
                    <td>{{ $serviceProponente->categoria }}</td>
                    <td>{{ $serviceReceptor->categoria }}</td>
                </tr>
                <tr>
                    <td><strong>Descrição</strong></td>
                    <td>{{ $serviceProponente->descricao }}</td>
                    <td>{{ $serviceReceptor->descricao }}</td>
                </tr>
                <tr>
                    <td><strong>Valor Sugerido</strong></td>
                    <td>R$ {{ number_format($serviceProponente->valor_sugerido ?? 0, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($serviceReceptor->valor_sugerido ?? 0, 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>Termos e Condições</h3>
        <p>
            As partes acima identificadas contratam mútua e irrestritamente a presente
            <strong>Troca de Serviços</strong>, mediante as seguintes condições:
        </p>
        <br>
        <p><strong>1.</strong> O Proponente se compromete a realizar o serviço descrito acima para o Receptor.</p>
        <p><strong>2.</strong> O Receptor se compromete a realizar o serviço descrito acima para o Proponente.</p>
        <p><strong>3.</strong> A execução dos serviços deverá ocorrer dentro do prazo acordado entre as partes.</p>
        <p><strong>4.</strong> A presente troca não envolve transferência monetária entre as partes.</p>
        <p><strong>5.</strong> As partes declaram estar de acordo com os termos deste contrato.</p>
    </div>

    <div class="section">
        <h3>Assinaturas</h3>
        <div class="signatures">
            <div class="signature-block">
                <div class="signature-line">
                    <p><strong>{{ $user1->name }}</strong></p>
                    <p>Proponente</p>
                    @if($contrato->assinatura_1_at)
                    <p style="font-size: 10pt; color: #666;">Assinado em {{ $contrato->assinatura_1_at->format('d/m/Y H:i') }}</p>
                    @endif
                </div>
            </div>
            <div class="signature-block">
                <div class="signature-line">
                    <p><strong>{{ $user2->name }}</strong></p>
                    <p>Receptor</p>
                    @if($contrato->assinatura_2_at)
                    <p style="font-size: 10pt; color: #666;">Assinado em {{ $contrato->assinatura_2_at->format('d/m/Y H:i') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Alphaview Serviços Residenciais &mdash; Plataforma de Troca de Serviços entre Condôminos</p>
        <p>Documento gerado em {{ $dataGeracao }}</p>
    </div>
</body>

</html>