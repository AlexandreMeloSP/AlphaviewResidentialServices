<?php

namespace Tests\Feature\Api;

use App\Models\Exchange;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_estatisticas_publicas_retornam_dados_corretos(): void
    {
        $usuario = User::forceCreate([
            'name' => 'Maria Santos',
            'email' => 'maria@example.com',
            'password' => 'Senha@1234',
            'status' => 'approved',
            'cpf' => '52998224725',
            'email_verified_at' => now(),
        ]);

        $servico1 = Service::forceCreate([
            'user_id' => $usuario->id,
            'titulo' => 'Serviço ativo',
            'descricao' => 'Descrição',
            'categoria' => 'Elétrica',
            'status' => 'active',
        ]);

        $servico2 = Service::forceCreate([
            'user_id' => $usuario->id,
            'titulo' => 'Serviço para troca',
            'descricao' => 'Descrição',
            'categoria' => 'Hidráulica',
            'status' => 'active',
        ]);

        Exchange::forceCreate([
            'service_proponente_id' => $servico1->id,
            'service_receptor_id' => $servico2->id,
            'user_proponente_id' => $usuario->id,
            'user_receptor_id' => $usuario->id,
            'status' => 'completed',
        ]);

        $response = $this->getJson('/api/dashboard/stats');

        $response->assertOk()
            ->assertJsonStructure([
                'total_servicos_concluidos',
                'total_usuarios',
                'total_servicos_ativos',
                'servicos_por_categoria',
                'trocas_por_mes',
                'servicos_recentes',
                'usuarios_recentes',
            ]);

        $this->assertEquals(2, $response->json('total_servicos_ativos'));
        $this->assertEquals(1, $response->json('total_usuarios'));
        $this->assertEquals(1, $response->json('total_servicos_concluidos'));
    }

    public function test_estatisticas_retornam_zero_sem_dados(): void
    {
        $response = $this->getJson('/api/dashboard/stats');

        $response->assertOk();

        $this->assertEquals(0, $response->json('total_servicos_concluidos'));
        $this->assertEquals(0, $response->json('total_usuarios'));
        $this->assertEquals(0, $response->json('total_servicos_ativos'));
        $this->assertEmpty($response->json('servicos_por_categoria'));
        $this->assertEmpty($response->json('servicos_recentes'));
        $this->assertEmpty($response->json('usuarios_recentes'));
    }

    public function test_estatisticas_contam_apenas_usuarios_aprovados(): void
    {
        User::forceCreate([
            'name' => 'Aprovado',
            'email' => 'aprovado@example.com',
            'password' => 'Senha@1234',
            'status' => 'approved',
            'cpf' => '52998224725',
            'email_verified_at' => now(),
        ]);

        User::forceCreate([
            'name' => 'Pendente',
            'email' => 'pendente@example.com',
            'password' => 'Senha@1234',
            'status' => 'pending',
            'cpf' => '98765432100',
        ]);

        $response = $this->getJson('/api/dashboard/stats');

        $response->assertOk();
        $this->assertEquals(1, $response->json('total_usuarios'));
    }

    public function test_estatisticas_contam_apenas_servicos_ativos(): void
    {
        $usuario = User::forceCreate([
            'name' => 'Maria',
            'email' => 'maria@example.com',
            'password' => 'Senha@1234',
            'status' => 'approved',
            'cpf' => '52998224725',
            'email_verified_at' => now(),
        ]);

        Service::forceCreate([
            'user_id' => $usuario->id,
            'titulo' => 'Ativo',
            'descricao' => 'D',
            'categoria' => 'C',
            'status' => 'active',
        ]);

        Service::forceCreate([
            'user_id' => $usuario->id,
            'titulo' => 'Inativo',
            'descricao' => 'D',
            'categoria' => 'C',
            'status' => 'inactive',
        ]);

        $response = $this->getJson('/api/dashboard/stats');

        $response->assertOk();
        $this->assertEquals(1, $response->json('total_servicos_ativos'));
    }

    public function test_dashboard_e_acessivel_sem_autenticacao(): void
    {
        $response = $this->getJson('/api/dashboard/stats');

        $response->assertOk();
    }

    public function test_servicos_por_categoria_sao_agrupados_corretamente(): void
    {
        $usuario = User::forceCreate([
            'name' => 'Maria',
            'email' => 'maria@example.com',
            'password' => 'Senha@1234',
            'status' => 'approved',
            'cpf' => '52998224725',
            'email_verified_at' => now(),
        ]);

        Service::forceCreate([
            'user_id' => $usuario->id,
            'titulo' => 'S1',
            'descricao' => 'D',
            'categoria' => 'Elétrica',
            'status' => 'active',
        ]);

        Service::forceCreate([
            'user_id' => $usuario->id,
            'titulo' => 'S2',
            'descricao' => 'D',
            'categoria' => 'Elétrica',
            'status' => 'active',
        ]);

        Service::forceCreate([
            'user_id' => $usuario->id,
            'titulo' => 'S3',
            'descricao' => 'D',
            'categoria' => 'Hidráulica',
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/dashboard/stats');

        $response->assertOk();
        $categorias = $response->json('servicos_por_categoria');
        $this->assertCount(2, $categorias);
        $this->assertEquals('Elétrica', $categorias[0]['categoria']);
        $this->assertEquals(2, $categorias[0]['total']);
    }

    public function test_dashboard_usuario_autenticado_retorna_dados(): void
    {
        $usuario = User::forceCreate([
            'name' => 'Maria',
            'email' => 'maria@example.com',
            'password' => 'Senha@1234',
            'status' => 'approved',
            'cpf' => '52998224725',
            'email_verified_at' => now(),
        ]);

        Service::forceCreate([
            'user_id' => $usuario->id,
            'titulo' => 'Meu serviço',
            'descricao' => 'D',
            'categoria' => 'Elétrica',
            'status' => 'active',
        ]);

        $response = $this->actingAs($usuario)
            ->getJson('/api/dashboard/user');

        $response->assertOk()
            ->assertJsonStructure([
                'meus_servicos',
                'servicos_restantes',
                'mensagens_count',
                'trocas_count',
                'contratos_count',
                'servicos_recentes',
            ]);

        $this->assertEquals(1, $response->json('meus_servicos'));
        $this->assertEquals(4, $response->json('servicos_restantes'));
    }
}
