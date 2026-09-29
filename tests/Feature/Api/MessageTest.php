<?php

namespace Tests\Feature\Api;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    private User $usuario1;
    private User $usuario2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->usuario1 = User::forceCreate([
            'name' => 'Ana Costa',
            'email' => 'ana@example.com',
            'password' => 'Senha@1234',
            'status' => 'approved',
            'cpf' => '52998224725',
            'email_verified_at' => now(),
        ]);

        $this->usuario2 = User::forceCreate([
            'name' => 'Carlos Souza',
            'email' => 'carlos@example.com',
            'password' => 'Senha@1234',
            'status' => 'approved',
            'cpf' => '98765432100',
            'email_verified_at' => now(),
        ]);
    }

    public function test_usuario_autenticado_pode_enviar_mensagem(): void
    {
        $response = $this->actingAs($this->usuario1)
            ->postJson('/api/messages', [
                'receiver_id' => $this->usuario2->id,
                'content' => 'Olá, tenho interesse no seu serviço.',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'sender_id',
                'receiver_id',
                'conteudo',
            ]);

        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->usuario1->id,
            'receiver_id' => $this->usuario2->id,
            'conteudo' => 'Olá, tenho interesse no seu serviço.',
        ]);
    }

    public function test_envio_requer_autenticacao(): void
    {
        $response = $this->postJson('/api/messages', [
            'receiver_id' => $this->usuario2->id,
            'content' => 'Mensagem anônima',
        ]);

        $response->assertStatus(401);
    }

    public function test_envio_requer_campos_obrigatorios(): void
    {
        $response = $this->actingAs($this->usuario1)
            ->postJson('/api/messages', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['receiver_id', 'content']);
    }

    public function test_nao_pode_enviar_mensagem_para_usuario_nao_aprovado(): void
    {
        $usuarioPendente = User::forceCreate([
            'name' => 'Pendente',
            'email' => 'pendente@example.com',
            'password' => 'Senha@1234',
            'status' => 'pending',
            'cpf' => '11122233309',
        ]);

        $response = $this->actingAs($this->usuario1)
            ->postJson('/api/messages', [
                'receiver_id' => $usuarioPendente->id,
                'content' => 'Mensagem para pendente',
            ]);

        $response->assertStatus(422)
            ->assertJson(['message' => 'O destinatário não está aprovado.']);
    }

    public function test_usuario_pode_listar_conversas(): void
    {
        Message::forceCreate([
            'sender_id' => $this->usuario1->id,
            'receiver_id' => $this->usuario2->id,
            'conteudo' => 'Primeira mensagem',
        ]);

        $response = $this->actingAs($this->usuario1)
            ->getJson('/api/messages/conversations');

        $response->assertOk()
            ->assertJsonCount(1);
    }

    public function test_usuario_pode_listar_mensagens_de_uma_conversa(): void
    {
        Message::forceCreate([
            'sender_id' => $this->usuario1->id,
            'receiver_id' => $this->usuario2->id,
            'conteudo' => 'Olá!',
        ]);

        Message::forceCreate([
            'sender_id' => $this->usuario2->id,
            'receiver_id' => $this->usuario1->id,
            'conteudo' => 'Oi, tudo bem?',
        ]);

        $response = $this->actingAs($this->usuario1)
            ->getJson("/api/messages/conversation/{$this->usuario2->id}");

        $response->assertOk()
            ->assertJsonCount(2);
    }

    public function test_mensagens_nao_lidas_sao_marcadas_como_lidas(): void
    {
        Message::forceCreate([
            'sender_id' => $this->usuario2->id,
            'receiver_id' => $this->usuario1->id,
            'conteudo' => 'Mensagem não lida',
            'lida' => false,
        ]);

        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->usuario2->id,
            'receiver_id' => $this->usuario1->id,
            'lida' => false,
        ]);

        $this->actingAs($this->usuario1)
            ->getJson("/api/messages/conversation/{$this->usuario2->id}");

        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->usuario2->id,
            'receiver_id' => $this->usuario1->id,
            'lida' => true,
        ]);
    }

    public function test_listagem_conversas_requer_autenticacao(): void
    {
        $response = $this->getJson('/api/messages/conversations');

        $response->assertStatus(401);
    }

    public function test_listagem_mensagens_requer_autenticacao(): void
    {
        $response = $this->getJson("/api/messages/conversation/{$this->usuario2->id}");

        $response->assertStatus(401);
    }

}
