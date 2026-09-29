<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private array $dadosCadastro = [
        'name' => 'João Silva',
        'email' => 'joao@example.com',
        'cpf' => '52998224725',
        'password' => 'Senha@1234',
        'password_confirmation' => 'Senha@1234',
    ];

    public function test_usuario_pode_se_cadastrar(): void
    {
        $response = $this->postJson('/api/auth/register', $this->dadosCadastro);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email', 'status'],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'joao@example.com',
            'status' => 'pending',
        ]);
    }

    public function test_cadastro_requer_campos_obrigatorios(): void
    {
        $response = $this->postJson('/api/auth/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'cpf', 'password']);
    }

    public function test_cadastro_rejeita_email_duplicado(): void
    {
        User::factory()->create(['email' => 'joao@example.com']);

        $response = $this->postJson('/api/auth/register', $this->dadosCadastro);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_cadastro_rejeita_cpf_duplicado(): void
    {
        User::factory()->create(['cpf' => '52998224725']);

        $response = $this->postJson('/api/auth/register', $this->dadosCadastro);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cpf']);
    }

    public function test_cadastro_rejeita_cpf_invalido(): void
    {
        $dados = [...$this->dadosCadastro, 'cpf' => '123'];

        $response = $this->postJson('/api/auth/register', $dados);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cpf']);
    }

    public function test_usuario_pode_fazer_login(): void
    {
        User::forceCreate([
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => 'Senha@1234',
            'status' => 'approved',
            'cpf' => '52998224725',
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'joao@example.com',
            'password' => 'Senha@1234',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
            ]);
    }

    public function test_login_rejeita_credenciais_invalidas(): void
    {
        User::forceCreate([
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => 'Senha@1234',
            'status' => 'approved',
            'cpf' => '52998224725',
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'joao@example.com',
            'password' => 'senha_errada',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_rejeita_email_nao_verificado(): void
    {
        User::forceCreate([
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => 'Senha@1234',
            'status' => 'approved',
            'cpf' => '52998224725',
            'email_verified_at' => null,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'joao@example.com',
            'password' => 'Senha@1234',
        ]);

        $response->assertStatus(403)
            ->assertJson(['requires_verification' => true]);
    }

    public function test_usuario_pode_fazer_logout(): void
    {
        $user = User::forceCreate([
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => 'Senha@1234',
            'status' => 'approved',
            'cpf' => '52998224725',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/auth/logout');

        $response->assertOk()
            ->assertJson(['message' => 'Sessão encerrada com sucesso.']);
    }

    public function test_usuario_autenticado_pode_consultar_seus_dados(): void
    {
        $user = User::forceCreate([
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => 'Senha@1234',
            'status' => 'approved',
            'cpf' => '52998224725',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/auth/user');

        $response->assertOk()
            ->assertJsonFragment([
                'name' => 'João Silva',
                'email' => 'joao@example.com',
            ]);
    }

    public function test_rota_protegida_requer_autenticacao(): void
    {
        $response = $this->getJson('/api/auth/user');

        $response->assertStatus(401);
    }

    public function test_cadastro_rejeita_senha_fraca(): void
    {
        $dados = [...$this->dadosCadastro, 'password' => 'fraca', 'password_confirmation' => 'fraca'];

        $response = $this->postJson('/api/auth/register', $dados);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_cadastro_rejeita_nome_com_caracteres_especiais(): void
    {
        $dados = [...$this->dadosCadastro, 'name' => 'João123'];

        $response = $this->postJson('/api/auth/register', $dados);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_verify_email_com_token_valido(): void
    {
        $user = User::forceCreate([
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => 'Senha@1234',
            'status' => 'pending',
            'cpf' => '52998224725',
            'email_verification_token' => 'tokenteste123',
        ]);

        $response = $this->getJson('/api/auth/verify-email/tokenteste123');

        $response->assertOk();
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_verify_email_com_token_invalido(): void
    {
        $response = $this->getJson('/api/auth/verify-email/tokeninvalido');

        $response->assertStatus(422);
    }
}
