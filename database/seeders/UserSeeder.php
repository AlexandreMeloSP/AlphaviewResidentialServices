<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Exchange;
use App\Models\Message;
use App\Models\Profile;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\User::unguarded(function () {
            $this->seedUsers();
        });
    }

    private function seedUsers(): void
    {
        $usuarios = [
            // 5 aprovados
            [
                'name' => 'Maria Silva',
                'email' => 'maria.silva@email.com',
                'cpf' => '11144477735',
                'status' => 'approved',
                'bio' => 'Eletricista residencial com 10 anos de experiência.',
                'telefone' => '(11) 99999-1001',
            ],
            [
                'name' => 'João Santos',
                'email' => 'joao.santos@email.com',
                'cpf' => '22255588846',
                'status' => 'approved',
                'bio' => 'Encanador e faz-tudo. Atendo finais de semana.',
                'telefone' => '(11) 99999-1002',
            ],
            [
                'name' => 'Ana Oliveira',
                'email' => 'ana.oliveira@email.com',
                'cpf' => '33366699957',
                'status' => 'approved',
                'bio' => 'Professora de reforço escolar para crianças.',
                'telefone' => '(11) 99999-1003',
            ],
            [
                'name' => 'Carlos Pereira',
                'email' => 'carlos.pereira@email.com',
                'cpf' => '44477700068',
                'status' => 'approved',
                'bio' => 'Jardineiro e paisagista. Trabalho com poda e manutenção.',
                'telefone' => '(11) 99999-1004',
            ],
            [
                'name' => 'Fernanda Lima',
                'email' => 'fernanda.lima@email.com',
                'cpf' => '55588811179',
                'status' => 'approved',
                'bio' => 'Diarista e passadeira. Referências disponíveis.',
                'telefone' => '(11) 99999-1005',
            ],
            // 5 pendentes
            [
                'name' => 'Roberto Souza',
                'email' => 'roberto.souza@email.com',
                'cpf' => '66699922280',
                'status' => 'pending',
                'bio' => 'Pintor residencial e comercial.',
                'telefone' => '(11) 99999-2001',
            ],
            [
                'name' => 'Juliana Costa',
                'email' => 'juliana.costa@email.com',
                'cpf' => '77700033391',
                'status' => 'pending',
                'bio' => 'Cabeleireira com atendimento domiciliar.',
                'telefone' => '(11) 99999-2002',
            ],
            [
                'name' => 'Marcos Ferreira',
                'email' => 'marcos.ferreira@email.com',
                'cpf' => '88811144402',
                'status' => 'pending',
                'bio' => 'Mecânico de automóveis. Faço revisões e reparos.',
                'telefone' => '(11) 99999-2003',
            ],
            [
                'name' => 'Patricia Almeida',
                'email' => 'patricia.almeida@email.com',
                'cpf' => '99922255513',
                'status' => 'pending',
                'bio' => 'Babá e cuidadora de idosos.',
                'telefone' => '(11) 99999-2004',
            ],
            [
                'name' => 'Lucas Martins',
                'email' => 'lucas.martins@email.com',
                'cpf' => '10293847560',
                'status' => 'pending',
                'bio' => 'Desenvolvedor web. Faço sites e sistemas.',
                'telefone' => '(11) 99999-2005',
            ],
        ];

        foreach ($usuarios as $dados) {
            $user = User::where('email', $dados['email'])->first()
                ?? User::forceCreate([
                    'name' => $dados['name'],
                    'email' => $dados['email'],
                    'password' => env('TEST_PASSWORD', 'password'),
                    'status' => $dados['status'],
                    'cpf' => $dados['cpf'],
                    'email_verified_at' => now(),
                ]);

            if (! Profile::where('user_id', $user->id)->exists()) {
                Profile::forceCreate([
                    'user_id' => $user->id,
                    'bio' => $dados['bio'],
                    'telefone' => $dados['telefone'],
                ]);
            }
        }

        $approved = User::whereIn('email', [
            'maria.silva@email.com',
            'joao.santos@email.com',
            'ana.oliveira@email.com',
            'carlos.pereira@email.com',
            'fernanda.lima@email.com',
        ])->get();

        $this->createServices($approved);
        $this->createMessages($approved);
        $this->createExchanges($approved);
        $this->createContracts($approved);
    }

    private function createServices($users): void
    {
        $servicos = [
            'maria.silva@email.com' => [
                ['titulo' => 'Instalação elétrica residencial', 'descricao' => 'Instalo quadro de força, tomadas e interruptores. Atendimento rápido e com garantia.', 'categoria' => 'Eletricista', 'valor' => 180.00],
                ['titulo' => 'Reparo de curto-circuito', 'descricao' => 'Diagnóstico e reparo de curto-circuito em fiação residencial.', 'categoria' => 'Eletricista', 'valor' => 120.00],
            ],
            'joao.santos@email.com' => [
                ['titulo' => 'Reparo de vazamento', 'descricao' => 'Reparo de torneiras, registros, encanamentos e caixas d\'água.', 'categoria' => 'Encanador', 'valor' => 150.00],
                ['titulo' => 'Desentupimento', 'descricao' => 'Desentupimento de ralos, pias e vasos sanitários.', 'categoria' => 'Encanador', 'valor' => 100.00],
            ],
            'ana.oliveira@email.com' => [
                ['titulo' => 'Aula de matemática', 'descricao' => 'Reforço escolar para ensino fundamental e médio. Preparação para vestibular.', 'categoria' => 'Aulas', 'valor' => 80.00],
                ['titulo' => 'Aula de inglês', 'descricao' => 'Aulas particulares de inglês para todos os níveis.', 'categoria' => 'Aulas', 'valor' => 90.00],
            ],
            'carlos.pereira@email.com' => [
                ['titulo' => 'Poda de árvores', 'descricao' => 'Poda de árvores frutíferas e ornamentais. Remoção de galhos secos.', 'categoria' => 'Jardinagem', 'valor' => 200.00],
                ['titulo' => 'Manutenção de jardim', 'descricao' => 'Corte de grama, adubação, plantio e manutenção de jardins.', 'categoria' => 'Jardinagem', 'valor' => 150.00],
            ],
            'fernanda.lima@email.com' => [
                ['titulo' => 'Limpeza residencial', 'descricao' => 'Limpeza completa de apartamentos e casas. Produtos inclusos.', 'categoria' => 'Limpeza', 'valor' => 120.00],
                ['titulo' => 'Passadeira profissional', 'descricao' => 'Passagem de roupa com acabamento profissional.', 'categoria' => 'Limpeza', 'valor' => 80.00],
            ],
        ];

        foreach ($servicos as $email => $lista) {
            $user = $users->firstWhere('email', $email);
            if (! $user) {
                continue;
            }
            foreach ($lista as $data) {
                $existe = Service::where('user_id', $user->id)
                    ->where('titulo', $data['titulo'])
                    ->exists();

                if (! $existe) {
                    Service::forceCreate([
                        'user_id' => $user->id,
                        'titulo' => $data['titulo'],
                        'descricao' => $data['descricao'],
                        'categoria' => $data['categoria'],
                        'valor_sugerido' => $data['valor'],
                        'status' => 'active',
                    ]);
                }
            }
        }
    }

    private function createMessages($users): void
    {
        $maria = $users->firstWhere('email', 'maria.silva@email.com');
        $joao = $users->firstWhere('email', 'joao.santos@email.com');
        $ana = $users->firstWhere('email', 'ana.oliveira@email.com');
        $carlos = $users->firstWhere('email', 'carlos.pereira@email.com');
        $fernanda = $users->firstWhere('email', 'fernanda.lima@email.com');

        $conversas = [
            [$maria, $ana, [
                'Olá Ana! Vi que você dá aulas de matemática. Teria disponibilidade para ajudar meu filho no ensino médio?',
                'Oi Maria! Claro, tenho sim. Em que dia e horário seria melhor?',
                'Terça e quinta, das 18h às 19h. Seria possível?',
                'Perfeito! Posso iniciar na próxima semana. O valor é R$80/hora.',
                'Ótimo, combinado! Muito obrigado!',
            ]],
            [$joao, $carlos, [
                'Oi Carlos, preciso de um electricista para trocar fiação da minha casa. Você faz esse serviço?',
                'Não faço parte, mas conheço a Maria Silva que é eletricista. Posso passar o contato.',
                'Seria ótimo, muito obrigado!',
            ]],
            [$fernanda, $maria, [
                'Maria, vou precisar de uma reforma elétrica no meu apartamento. Quanto cobraria?',
                'Oi Fernanda! Preciso ver o local primeiro. Posso ir amanhã para avaliar?',
                'Pode sim! Estarei em casa pela manhã.',
            ]],
        ];

        foreach ($conversas as [$sender, $receiver, $mensagens]) {
            $created = now();
            foreach ($mensagens as $i => $conteudo) {
                $fromSender = $i % 2 === 0;
                $senderId = $fromSender ? $sender->id : $receiver->id;
                $receiverId = $fromSender ? $receiver->id : $sender->id;

                $existe = Message::where('sender_id', $senderId)
                    ->where('receiver_id', $receiverId)
                    ->where('conteudo', $conteudo)
                    ->exists();

                if (! $existe) {
                    Message::forceCreate([
                        'sender_id' => $senderId,
                        'receiver_id' => $receiverId,
                        'conteudo' => $conteudo,
                        'lida' => $i < count($mensagens) - 1,
                        'created_at' => $created->copy()->addMinutes($i * 5),
                        'updated_at' => $created->copy()->addMinutes($i * 5),
                    ]);
                }
            }
        }
    }

    private function createExchanges($users): void
    {
        $maria = $users->firstWhere('email', 'maria.silva@email.com');
        $ana = $users->firstWhere('email', 'ana.oliveira@email.com');
        $joao = $users->firstWhere('email', 'joao.santos@email.com');
        $fernanda = $users->firstWhere('email', 'fernanda.lima@email.com');

        $servicoMaria = Service::where('user_id', $maria->id)->first();
        $servicoAna = Service::where('user_id', $ana->id)->first();
        $servicoJoao = Service::where('user_id', $joao->id)->first();
        $servicoFernanda = Service::where('user_id', $fernanda->id)->first();

        if (! $servicoMaria || ! $servicoAna || ! $servicoJoao || ! $servicoFernanda) {
            return;
        }

        $exchanges = [
            [
                'user_proponente_id' => $maria->id,
                'user_receptor_id' => $ana->id,
                'service_proponente_id' => $servicoMaria->id,
                'service_receptor_id' => $servicoAna->id,
                'status' => 'confirmed',
            ],
            [
                'user_proponente_id' => $joao->id,
                'user_receptor_id' => $fernanda->id,
                'service_proponente_id' => $servicoJoao->id,
                'service_receptor_id' => $servicoFernanda->id,
                'status' => 'completed',
            ],
            [
                'user_proponente_id' => $ana->id,
                'user_receptor_id' => $maria->id,
                'service_proponente_id' => $servicoAna->id,
                'service_receptor_id' => $servicoMaria->id,
                'status' => 'pending',
            ],
        ];

        foreach ($exchanges as $dados) {
            $existe = Exchange::where('user_proponente_id', $dados['user_proponente_id'])
                ->where('user_receptor_id', $dados['user_receptor_id'])
                ->exists();

            if (! $existe) {
                Exchange::forceCreate($dados);
            }
        }
    }

    private function createContracts($users): void
    {
        $exchangeCompleted = Exchange::where('status', 'completed')->first();

        if ($exchangeCompleted && ! Contract::where('exchange_id', $exchangeCompleted->id)->exists()) {
            Contract::forceCreate([
                'exchange_id' => $exchangeCompleted->id,
                'user_1_id' => $exchangeCompleted->user_proponente_id,
                'user_2_id' => $exchangeCompleted->user_receptor_id,
                'status' => 'signed',
                'assinatura_1_at' => now()->subDays(2),
                'assinatura_2_at' => now()->subDays(1),
                'conteudo_pdf' => 'Contrato de troca de serviços entre as partes.',
            ]);
        }

        $exchangePending = Exchange::where('status', 'pending')->first();

        if ($exchangePending && ! Contract::where('exchange_id', $exchangePending->id)->exists()) {
            Contract::forceCreate([
                'exchange_id' => $exchangePending->id,
                'user_1_id' => $exchangePending->user_proponente_id,
                'user_2_id' => $exchangePending->user_receptor_id,
                'status' => 'draft',
            ]);
        }
    }
}
