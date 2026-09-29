<?php

namespace Tests\Feature\Api;

use App\Models\Contract;
use App\Models\Exchange;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractTest extends TestCase
{
    use RefreshDatabase;

    private User $user1;
    private User $user2;
    private Exchange $exchange;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user1 = User::forceCreate([
            'name' => 'Ana Costa',
            'email' => 'ana@example.com',
            'password' => 'Senha@1234',
            'status' => 'approved',
            'cpf' => '52998224725',
            'email_verified_at' => now(),
        ]);

        $this->user2 = User::forceCreate([
            'name' => 'Carlos Souza',
            'email' => 'carlos@example.com',
            'password' => 'Senha@1234',
            'status' => 'approved',
            'cpf' => '98765432100',
            'email_verified_at' => now(),
        ]);

        $servico1 = Service::forceCreate([
            'user_id' => $this->user1->id,
            'titulo' => 'Elétrica',
            'descricao' => 'D',
            'categoria' => 'Elétrica',
            'status' => 'active',
        ]);

        $servico2 = Service::forceCreate([
            'user_id' => $this->user2->id,
            'titulo' => 'Hidráulica',
            'descricao' => 'D',
            'categoria' => 'Hidráulica',
            'status' => 'active',
        ]);

        $this->exchange = Exchange::forceCreate([
            'service_proponente_id' => $servico1->id,
            'service_receptor_id' => $servico2->id,
            'user_proponente_id' => $this->user1->id,
            'user_receptor_id' => $this->user2->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_participante_pode_listar_contratos(): void
    {
        Contract::forceCreate([
            'exchange_id' => $this->exchange->id,
            'user_1_id' => $this->user1->id,
            'user_2_id' => $this->user2->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user1)
            ->getJson('/api/contracts');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_contrato_requer_autenticacao(): void
    {
        $response = $this->getJson('/api/contracts');

        $response->assertStatus(401);
    }

    public function test_participante_pode_ver_contrato(): void
    {
        $contrato = Contract::forceCreate([
            'exchange_id' => $this->exchange->id,
            'user_1_id' => $this->user1->id,
            'user_2_id' => $this->user2->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user1)
            ->getJson("/api/contracts/{$contrato->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'id',
                'exchange_id',
                'user_1_id',
                'user_2_id',
                'status',
            ]);
    }

    public function test_nao_participante_nao_pode_ver_contrato(): void
    {
        $terceiro = User::forceCreate([
            'name' => 'Terceiro',
            'email' => 'terceiro@example.com',
            'password' => 'Senha@1234',
            'status' => 'approved',
            'cpf' => '11122233309',
            'email_verified_at' => now(),
        ]);

        $contrato = Contract::forceCreate([
            'exchange_id' => $this->exchange->id,
            'user_1_id' => $this->user1->id,
            'user_2_id' => $this->user2->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($terceiro)
            ->getJson("/api/contracts/{$contrato->id}");

        $response->assertStatus(403);
    }

    public function test_admin_pode_ver_contrato_de_outros(): void
    {
        $admin = User::forceCreate([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => 'Senha@1234',
            'status' => 'approved',
            'is_admin' => true,
            'cpf' => '11122233309',
            'email_verified_at' => now(),
        ]);

        $contrato = Contract::forceCreate([
            'exchange_id' => $this->exchange->id,
            'user_1_id' => $this->user1->id,
            'user_2_id' => $this->user2->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($admin)
            ->getJson("/api/contracts/{$contrato->id}");

        $response->assertOk();
    }

    public function test_participante_pode_assinar_contrato(): void
    {
        $contrato = Contract::forceCreate([
            'exchange_id' => $this->exchange->id,
            'user_1_id' => $this->user1->id,
            'user_2_id' => $this->user2->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user1)
            ->postJson("/api/contracts/{$contrato->id}/sign");

        $response->assertOk();
        $this->assertNotNull($contrato->fresh()->assinatura_1_at);
    }

    public function test_ambas_assinaturas_mudam_status_para_signed(): void
    {
        $contrato = Contract::forceCreate([
            'exchange_id' => $this->exchange->id,
            'user_1_id' => $this->user1->id,
            'user_2_id' => $this->user2->id,
            'status' => 'draft',
            'assinatura_1_at' => now(),
        ]);

        $response = $this->actingAs($this->user2)
            ->postJson("/api/contracts/{$contrato->id}/sign");

        $response->assertOk();
        $this->assertEquals('signed', $contrato->fresh()->status);
        $this->assertEquals('completed', $this->exchange->fresh()->status);
    }

    public function test_nao_pode_assinar_contrato_cancelado(): void
    {
        $contrato = Contract::forceCreate([
            'exchange_id' => $this->exchange->id,
            'user_1_id' => $this->user1->id,
            'user_2_id' => $this->user2->id,
            'status' => 'cancelled',
        ]);

        $response = $this->actingAs($this->user1)
            ->postJson("/api/contracts/{$contrato->id}/sign");

        $response->assertStatus(422);
    }

    public function test_nao_pode_assinar_duas_vezes(): void
    {
        $contrato = Contract::forceCreate([
            'exchange_id' => $this->exchange->id,
            'user_1_id' => $this->user1->id,
            'user_2_id' => $this->user2->id,
            'status' => 'draft',
            'assinatura_1_at' => now(),
        ]);

        $response = $this->actingAs($this->user1)
            ->postJson("/api/contracts/{$contrato->id}/sign");

        $response->assertStatus(422);
    }

    public function test_gerar_pdf_requer_contrato_assinado(): void
    {
        $contrato = Contract::forceCreate([
            'exchange_id' => $this->exchange->id,
            'user_1_id' => $this->user1->id,
            'user_2_id' => $this->user2->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user1)
            ->postJson("/api/contracts/{$contrato->id}/pdf");

        $response->assertStatus(422);
    }

    public function test_download_pdf_sem_pdf_retorna_404(): void
    {
        $contrato = Contract::forceCreate([
            'exchange_id' => $this->exchange->id,
            'user_1_id' => $this->user1->id,
            'user_2_id' => $this->user2->id,
            'status' => 'signed',
        ]);

        $response = $this->actingAs($this->user1)
            ->getJson("/api/contracts/{$contrato->id}/download");

        $response->assertStatus(404);
    }
}
