<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Exchange;
use App\Models\Message;
use App\Models\Profile;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    private array $categorias = [
        'Encanador', 'Eletricista', 'Pintor', 'Marceneiro',
        'Jardinagem', 'Limpeza', 'Manutenção', 'Tecnologia',
    ];

    private array $cpfs = [
        '52998224725', '98765432100', '11122233309',
        '44455566677', '77788899900', '12345678909',
        '23456789018', '34567890127', '67890123456',
        '89012345678',
    ];

    private int $cpfIndex = 0;

    public function run(): void
    {
        User::unguarded(function () {
            $this->seed();
        });
    }

    private function seed(): void
    {
        $admin = User::where('email', 'admin@alphaview.com')->first();

        $approvedUsers = $this->createApprovedUsers();
        $pendingUsers = $this->createPendingUsers();
        $rejectedUsers = $this->createRejectedUsers();
        $deletedUser = $this->createDeletedUser();

        $this->createServices($approvedUsers);
        $this->createMessages($approvedUsers);
        $this->createExchanges($approvedUsers);
        $this->createContracts($approvedUsers);
    }

    private function nextCpf(): string
    {
        $cpf = $this->cpfs[$this->cpfIndex] ?? str_pad((string) ($this->cpfIndex + 1), 11, '0', STR_PAD_LEFT);
        $this->cpfIndex++;

        return $cpf;
    }

    private function createApprovedUsers(): array
    {
        $users = [
            ['name' => 'Carlos Silva', 'email' => 'carlos@test.com', 'bio' => 'Eletricista residencial com 10 anos de experiência.'],
            ['name' => 'Ana Oliveira', 'email' => 'ana@test.com', 'bio' => 'Professora de matemática e física.'],
            ['name' => 'Pedro Santos', 'email' => 'pedro@test.com', 'bio' => 'Encanador e reparador hidráulico.'],
            ['name' => 'Maria Costa', 'email' => 'maria@test.com', 'bio' => 'Jardineira e paisagista.'],
            ['name' => 'João Pereira', 'email' => 'joao@test.com', 'bio' => 'Técnico de TI e redes.'],
            ['name' => 'Fernanda Lima', 'email' => 'fernanda@test.com', 'bio' => 'Pintora e decoradora de interiores.'],
        ];

        $result = [];
        foreach ($users as $i => $data) {
            $user = User::where('email', $data['email'])->first()
                ?? User::forceCreate([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => 'Senha@1234',
                    'status' => 'approved',
                    'cpf' => $this->nextCpf(),
                    'email_verified_at' => now()->subDays(rand(1, 30)),
                ]);

            if (! Profile::where('user_id', $user->id)->exists()) {
                Profile::forceCreate([
                    'user_id' => $user->id,
                    'bio' => $data['bio'],
                ]);
            }

            if (! $user->roles()->where('role_id', 2)->exists()) {
                $user->roles()->attach(2);
            }
            $result[] = $user;
        }

        return $result;
    }

    private function createPendingUsers(): array
    {
        $users = [
            ['name' => 'Lucas Almeida', 'email' => 'lucas@test.com'],
            ['name' => 'Juliana Ribeiro', 'email' => 'juliana@test.com'],
        ];

        $result = [];
        foreach ($users as $data) {
            $user = User::where('email', $data['email'])->first()
                ?? User::forceCreate([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => 'Senha@1234',
                    'status' => 'pending',
                    'cpf' => $this->nextCpf(),
                    'email_verified_at' => now(),
                ]);

            if (! Profile::where('user_id', $user->id)->exists()) {
                Profile::forceCreate(['user_id' => $user->id]);
            }
            $result[] = $user;
        }

        return $result;
    }

    private function createRejectedUsers(): array
    {
        $users = [
            ['name' => 'Roberto Ferreira', 'email' => 'roberto@test.com'],
            ['name' => 'Camila Souza', 'email' => 'camila@test.com'],
        ];

        $result = [];
        foreach ($users as $data) {
            $user = User::where('email', $data['email'])->first()
                ?? User::forceCreate([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => 'Senha@1234',
                    'status' => 'rejected',
                    'cpf' => $this->nextCpf(),
                    'email_verified_at' => now(),
                ]);

            if (! Profile::where('user_id', $user->id)->exists()) {
                Profile::forceCreate(['user_id' => $user->id]);
            }
            $result[] = $user;
        }

        return $result;
    }

    private function createDeletedUser(): ?User
    {
        $user = User::where('email', 'marcos@test.com')->first()
            ?? User::forceCreate([
                'name' => 'Marcos Vieira',
                'email' => 'marcos@test.com',
                'password' => 'Senha@1234',
                'status' => 'approved',
                'cpf' => $this->nextCpf(),
                'email_verified_at' => now(),
            ]);

        if (! Profile::where('user_id', $user->id)->exists()) {
            Profile::forceCreate(['user_id' => $user->id]);
        }

        $user->delete();

        return $user;
    }

    private function createServices(array $users): void
    {
        $servicosData = [
            0 => [
                ['titulo' => 'Instalação elétrica residencial', 'descricao' => 'Instalo quadro de força, tomadas e interruptores. Atendimento rápido e com garantia.', 'categoria' => 'Eletricista', 'valor' => 180.00],
                ['titulo' => 'Reparo de curto-circuito', 'descricao' => 'Diagnóstico e reparo de curto-circuito em fiação residencial.', 'categoria' => 'Eletricista', 'valor' => 120.00],
            ],
            1 => [
                ['titulo' => 'Aula de matemática', 'descricao' => 'Reforço escolar para ensino fundamental e médio. Preparação para vestibular.', 'categoria' => 'Aulas', 'valor' => 80.00],
                ['titulo' => 'Aula de inglês', 'descricao' => 'Aulas particulares de inglês para todos os níveis.', 'categoria' => 'Aulas', 'valor' => 90.00],
            ],
            2 => [
                ['titulo' => 'Reparo de vazamento', 'descricao' => 'Reparo de torneiras, registros, encanamentos e caixas d\'água.', 'categoria' => 'Encanador', 'valor' => 150.00],
                ['titulo' => 'Desentupimento', 'descricao' => 'Desentupimento de ralos, pias e vasos sanitários.', 'categoria' => 'Encanador', 'valor' => 100.00],
            ],
            3 => [
                ['titulo' => 'Poda de árvores', 'descricao' => 'Poda de árvores frutíferas e ornamentais. Remoção de galhos secos.', 'categoria' => 'Jardinagem', 'valor' => 200.00],
            ],
            4 => [
                ['titulo' => 'Formatação de computador', 'descricao' => 'Formatação, instalação de programas e limpeza de vírus.', 'categoria' => 'Tecnologia', 'valor' => 150.00],
                ['titulo' => 'Configuração de rede WiFi', 'descricao' => 'Instalação e configuração de roteadores e redes WiFi.', 'categoria' => 'Tecnologia', 'valor' => 100.00],
            ],
            5 => [
                ['titulo' => 'Pintura de apartamento', 'descricao' => 'Pintura interna e externa de apartamentos e casas.', 'categoria' => 'Pintor', 'valor' => 300.00],
            ],
        ];

        foreach ($servicosData as $userIdx => $servicos) {
            $user = $users[$userIdx] ?? null;
            if (! $user) {
                continue;
            }

            foreach ($servicos as $data) {
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

    private function createMessages(array $users): void
    {
        if (count($users) < 2) {
            return;
        }

        $conversas = [
            [$users[0], $users[1], [
                'Olá Ana! Vi que você dá aulas de matemática. Teria disponibilidade para ajudar meu filho no ensino médio?',
                'Oi Carlos! Claro, tenho sim. Em que dia e horário seria melhor?',
                'Terça e quinta, das 18h às 19h. Seria possível?',
                'Perfeito! Posso iniciar na próxima semana. O valor é R$80/hora.',
                'Ótimo, combinado! Muito obrigado!',
            ]],
            [$users[0], $users[2], [
                'Oi Pedro, tenho um vazamento no banheiro. Poderia dar uma olhada?',
                'Claro Carlos! Posso ir amanhã de manhã. É em que apartamento?',
                'Apartamento 302. Obrigado!',
            ]],
            [$users[1], $users[4], [
                'João, preciso formatar meu notebook. Você faz esse serviço?',
                'Fazo sim, Ana! Leva cerca de 2 horas. Posso ir na quarta?',
                'Fica bom para mim!',
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

    private function createExchanges(array $users): void
    {
        if (count($users) < 4) {
            return;
        }

        $servicoEletricista = Service::where('user_id', $users[0]->id)->first();
        $servicoAula = Service::where('user_id', $users[1]->id)->first();
        $servicoEncanador = Service::where('user_id', $users[2]->id)->first();
        $servicoTecnologia = Service::where('user_id', $users[4]->id)->first();

        if (! $servicoEletricista || ! $servicoAula || ! $servicoEncanador || ! $servicoTecnologia) {
            return;
        }

        $exchanges = [
            [
                'user_proponente_id' => $users[0]->id,
                'user_receptor_id' => $users[1]->id,
                'service_proponente_id' => $servicoEletricista->id,
                'service_receptor_id' => $servicoAula->id,
                'status' => 'confirmed',
            ],
            [
                'user_proponente_id' => $users[2]->id,
                'user_receptor_id' => $users[4]->id,
                'service_proponente_id' => $servicoEncanador->id,
                'service_receptor_id' => $servicoTecnologia->id,
                'status' => 'completed',
            ],
            [
                'user_proponente_id' => $users[1]->id,
                'user_receptor_id' => $users[3]->id,
                'service_proponente_id' => $servicoAula->id,
                'service_receptor_id' => Service::where('user_id', $users[3]->id)->first()?->id ?? $servicoAula->id,
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

    private function createContracts(array $users): void
    {
        if (count($users) < 5) {
            return;
        }

        $exchange = Exchange::where('status', 'completed')->first();

        if ($exchange && ! Contract::where('exchange_id', $exchange->id)->exists()) {
            Contract::forceCreate([
                'exchange_id' => $exchange->id,
                'user_1_id' => $exchange->user_proponente_id,
                'user_2_id' => $exchange->user_receptor_id,
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
