<?php

namespace Tests\Feature\Api;

use App\Models\Exchange;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExchangeTest extends TestCase
{
    use RefreshDatabase;

    private User $proponente;
    private User $receptor;
    private Service $servicoProponente;
    private Service $servicoReceptor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->proponente = User::forceCreate([
            'name' => 'Ana Costa',
            'email' => 'ana@example.com',
            'password' => 'Senha@1234',
            'status' => 'approved',
            'cpf' => '52998224725',
            'email_verified_at' => now(),
        ]);

        $this->receptor = User::forceCreate([
            'name' => 'Carlos Souza',
            'email' => 'carlos@example.com',
            'password' => 'Senha@1234',
            'status' => 'approved',
            'cpf' => '98765432100',
            'email_verified_at' => now(),
        ]);

        $this->servicoProponente = Service::forceCreate([
            'user_id' => $this->proponente->id,
            'titulo' => 'Elétrica',
            'descricao' => 'Reparos elétricos',
            'categoria' => 'Elétrica',
            'status' => 'active',
        ]);

        $this->servicoReceptor = Service::forceCreate([
            'user_id' => $this->receptor->id,
            'titulo' => 'Hidráulica',
            'descricao' => 'Reparos hidráulicos',
            'categoria' => 'Hidráulica',
            'status' => 'active',
        ]);
    }

    public function test_usuario_aprovado_pode_propor_troca(): void
    {
        $response = $this->actingAs($this->proponente)
            ->postJson('/api/exchanges', [
                'service_proponente_id' => $this->servicoProponente->id,
                'service_receptor_id' => $this->servicoReceptor->id,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'status',
                'service_proponente_id',
                'service_receptor_id',
                'user_proponente_id',
                'user_receptor_id',
            ]);

        $this->assertDatabaseHas('exchanges', [
            'user_proponente_id' => $this->proponente->id,
            'user_receptor_id' => $this->receptor->id,
            'status' => 'pending',
        ]);
    }

    public function test_troca_requer_autenticacao(): void
    {
        $response = $this->postJson('/api/exchanges', [
            'service_proponente_id' => $this->servicoProponente->id,
            'service_receptor_id' => $this->servicoReceptor->id,
        ]);

        $response->assertStatus(401);
    }

    public function test_nao_pode_trocar_com_si_mesmo(): void
    {
        $meuServico2 = Service::forceCreate([
            'user_id' => $this->proponente->id,
            'titulo' => 'Outro serviço',
            'descricao' => 'D',
            'categoria' => 'C',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->proponente)
            ->postJson('/api/exchanges', [
                'service_proponente_id' => $this->servicoProponente->id,
                'service_receptor_id' => $meuServico2->id,
            ]);

        $response->assertStatus(422);
    }

    public function test_servico_proponente_deve_pertencer_ao_usuario(): void
    {
        $response = $this->actingAs($this->proponente)
            ->postJson('/api/exchanges', [
                'service_proponente_id' => $this->servicoReceptor->id,
                'service_receptor_id' => $this->servicoProponente->id,
            ]);

        $response->assertStatus(403);
    }

    public function test_usuario_pode_listar_suas_trocas(): void
    {
        Exchange::forceCreate([
            'service_proponente_id' => $this->servicoProponente->id,
            'service_receptor_id' => $this->servicoReceptor->id,
            'user_proponente_id' => $this->proponente->id,
            'user_receptor_id' => $this->receptor->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->proponente)
            ->getJson('/api/exchanges');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_usuario_pode_confirmar_troca(): void
    {
        $troca = Exchange::forceCreate([
            'service_proponente_id' => $this->servicoProponente->id,
            'service_receptor_id' => $this->servicoReceptor->id,
            'user_proponente_id' => $this->proponente->id,
            'user_receptor_id' => $this->receptor->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->receptor)
            ->postJson("/api/exchanges/{$troca->id}/confirm");

        $response->assertOk()
            ->assertJson(['status' => 'confirmed']);

        $this->assertDatabaseHas('exchanges', [
            'id' => $troca->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_usuario_pode_cancelar_troca(): void
    {
        $troca = Exchange::forceCreate([
            'service_proponente_id' => $this->servicoProponente->id,
            'service_receptor_id' => $this->servicoReceptor->id,
            'user_proponente_id' => $this->proponente->id,
            'user_receptor_id' => $this->receptor->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->proponente)
            ->postJson("/api/exchanges/{$troca->id}/cancel");

        $response->assertOk()
            ->assertJson(['status' => 'cancelled']);
    }

    public function test_nao_participante_nao_pode_confirmar(): void
    {
        $terceiro = User::forceCreate([
            'name' => 'Terceiro',
            'email' => 'terceiro@example.com',
            'password' => 'Senha@1234',
            'status' => 'approved',
            'cpf' => '11122233309',
            'email_verified_at' => now(),
        ]);

        $troca = Exchange::forceCreate([
            'service_proponente_id' => $this->servicoProponente->id,
            'service_receptor_id' => $this->servicoReceptor->id,
            'user_proponente_id' => $this->proponente->id,
            'user_receptor_id' => $this->receptor->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($terceiro)
            ->postJson("/api/exchanges/{$troca->id}/confirm");

        $response->assertStatus(403);
    }

    public function test_nao_pode_confirmar_troca_ja_confirmada(): void
    {
        $troca = Exchange::forceCreate([
            'service_proponente_id' => $this->servicoProponente->id,
            'service_receptor_id' => $this->servicoReceptor->id,
            'user_proponente_id' => $this->proponente->id,
            'user_receptor_id' => $this->receptor->id,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($this->receptor)
            ->postJson("/api/exchanges/{$troca->id}/confirm");

        $response->assertStatus(422);
    }

    public function test_nao_pode_cancelar_troca_completed(): void
    {
        $troca = Exchange::forceCreate([
            'service_proponente_id' => $this->servicoProponente->id,
            'service_receptor_id' => $this->servicoReceptor->id,
            'user_proponente_id' => $this->proponente->id,
            'user_receptor_id' => $this->receptor->id,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->proponente)
            ->postJson("/api/exchanges/{$troca->id}/cancel");

        $response->assertStatus(422);
    }
}
