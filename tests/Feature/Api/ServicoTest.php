<?php

namespace Tests\Feature\Api;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicoTest extends TestCase
{
    use RefreshDatabase;

    private User $usuario;

    protected function setUp(): void
    {
        parent::setUp();

        $this->usuario = User::forceCreate([
            'name' => 'Maria Santos',
            'email' => 'maria@example.com',
            'password' => 'Senha@1234',
            'status' => 'approved',
            'cpf' => '52998224725',
            'email_verified_at' => now(),
        ]);
    }

    private function dadosServico(): array
    {
        return [
            'titulo' => 'Reparo elétrico residencial',
            'descricao' => 'Executo reparos em fiação e disjuntores residenciais',
            'categoria' => 'Elétrica',
            'valor_sugerido' => 150.00,
        ];
    }

    public function test_usuario_autenticado_pode_criar_servico(): void
    {
        $response = $this->actingAs($this->usuario)
            ->postJson('/api/servicos', $this->dadosServico());

        $response->assertStatus(201)
            ->assertJsonFragment(['titulo' => 'Reparo elétrico residencial']);

        $this->assertDatabaseHas('services', [
            'user_id' => $this->usuario->id,
            'titulo' => 'Reparo elétrico residencial',
        ]);
    }

    public function test_criacao_requer_autenticacao(): void
    {
        $response = $this->postJson('/api/servicos', $this->dadosServico());

        $response->assertStatus(401);
    }

    public function test_usuario_nao_aprovado_nao_pode_criar_servico(): void
    {
        $usuarioPendente = User::forceCreate([
            'name' => 'Pedro Lima',
            'email' => 'pedro@example.com',
            'password' => 'Senha@1234',
            'status' => 'pending',
            'cpf' => '98765432100',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($usuarioPendente)
            ->postJson('/api/servicos', $this->dadosServico());

        $response->assertStatus(403);
    }

    public function test_usuario_nao_pode_exceder_limite_de_5_servicos(): void
    {
        for ($i = 0; $i < 5; $i++) {
            Service::forceCreate([
                'user_id' => $this->usuario->id,
                'titulo' => "Serviço {$i}",
                'descricao' => "Descrição do serviço {$i}",
                'categoria' => 'Teste',
                'status' => 'active',
            ]);
        }

        $response = $this->actingAs($this->usuario)
            ->postJson('/api/servicos', $this->dadosServico());

        $response->assertStatus(403);
    }

    public function test_usuario_pode_listar_seus_servicos(): void
    {
        Service::forceCreate([
            'user_id' => $this->usuario->id,
            'titulo' => 'Meu serviço',
            'descricao' => 'Descrição',
            'categoria' => 'Teste',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->usuario)
            ->getJson('/api/servicos/mine');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_listagem_publica_retorna_servicos_ativos(): void
    {
        Service::forceCreate([
            'user_id' => $this->usuario->id,
            'titulo' => 'Serviço ativo',
            'descricao' => 'Descrição',
            'categoria' => 'Teste',
            'status' => 'active',
        ]);

        Service::forceCreate([
            'user_id' => $this->usuario->id,
            'titulo' => 'Serviço inativo',
            'descricao' => 'Descrição',
            'categoria' => 'Teste',
            'status' => 'inactive',
        ]);

        $response = $this->getJson('/api/servicos');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_usuario_pode_atualizar_seu_servico(): void
    {
        $servico = Service::forceCreate([
            'user_id' => $this->usuario->id,
            'titulo' => 'Serviço original',
            'descricao' => 'Descrição',
            'categoria' => 'Teste',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->usuario)
            ->putJson("/api/servicos/{$servico->id}", [
                'titulo' => 'Serviço atualizado',
                'descricao' => 'Descrição atualizada',
                'categoria' => 'Teste',
            ]);

        $response->assertOk()
            ->assertJsonFragment(['titulo' => 'Serviço atualizado']);

        $this->assertDatabaseHas('services', [
            'id' => $servico->id,
            'titulo' => 'Serviço atualizado',
        ]);
    }

    public function test_usuario_nao_pode_atualizar_servico_de_outro(): void
    {
        $outroUsuario = User::forceCreate([
            'name' => 'Outro',
            'email' => 'outro@example.com',
            'password' => 'Senha@1234',
            'status' => 'approved',
            'cpf' => '98765432100',
            'email_verified_at' => now(),
        ]);

        $servico = Service::forceCreate([
            'user_id' => $outroUsuario->id,
            'titulo' => 'Serviço do outro',
            'descricao' => 'Descrição',
            'categoria' => 'Teste',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->usuario)
            ->putJson("/api/servicos/{$servico->id}", [
                'titulo' => 'Tentativa de invasão',
                'descricao' => 'Descrição',
                'categoria' => 'Teste',
            ]);

        $response->assertStatus(403);
    }

    public function test_usuario_pode_remover_seu_servico(): void
    {
        $servico = Service::forceCreate([
            'user_id' => $this->usuario->id,
            'titulo' => 'Serviço a remover',
            'descricao' => 'Descrição',
            'categoria' => 'Teste',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->usuario)
            ->deleteJson("/api/servicos/{$servico->id}");

        $response->assertOk()
            ->assertJson(['message' => 'Serviço removido com sucesso.']);

        $this->assertSoftDeleted('services', ['id' => $servico->id]);
    }

    public function test_usuario_nao_pode_remover_servico_de_outro(): void
    {
        $outroUsuario = User::forceCreate([
            'name' => 'Outro',
            'email' => 'outro@example.com',
            'password' => 'Senha@1234',
            'status' => 'approved',
            'cpf' => '98765432100',
            'email_verified_at' => now(),
        ]);

        $servico = Service::forceCreate([
            'user_id' => $outroUsuario->id,
            'titulo' => 'Serviço protegido',
            'descricao' => 'Descrição',
            'categoria' => 'Teste',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->usuario)
            ->deleteJson("/api/servicos/{$servico->id}");

        $response->assertStatus(403);
    }

    public function test_criacao_requer_campos_obrigatorios(): void
    {
        $response = $this->actingAs($this->usuario)
            ->postJson('/api/servicos', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['titulo', 'descricao', 'categoria']);
    }

    public function test_busca_filtra_servicos(): void
    {
        Service::forceCreate([
            'user_id' => $this->usuario->id,
            'titulo' => 'Eletricista residencial',
            'descricao' => 'Reparos em fiação',
            'categoria' => 'Elétrica',
            'status' => 'active',
        ]);

        Service::forceCreate([
            'user_id' => $this->usuario->id,
            'titulo' => 'Encanador',
            'descricao' => 'Conserto de vazamentos',
            'categoria' => 'Hidráulica',
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/servicos?busca=eletricista');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Eletricista residencial', $response->json('data.0.titulo'));
    }
}
