# Documentação de Testes - Laravel Clean Architecture

## Estrutura de Testes

```
tests/
├── Unit/                           # Testes unitários (isolados, sem dependências)
│   ├── Domain/
│   │   └── User/
│   │       ├── Entities/
│   │       │   └── UserTest.php
│   │       └── ValueObjects/
│   │           ├── EmailTest.php
│   │           └── UserIdTest.php
│   └── Application/
│       └── User/
│           └── UseCases/
│               ├── CreateUserUseCaseTest.php
│               ├── UpdateUserUseCaseTest.php
│               ├── DeleteUserUseCaseTest.php
│               ├── GetUserUseCaseTest.php
│               └── ListUsersUseCaseTest.php
│
├── Integration/                    # Testes de integração (com banco de dados)
│   └── Infrastructure/
│       └── Persistence/
│           └── EloquentUserRepositoryTest.php
│
└── Feature/                        # Testes E2E (API completa)
    └── Api/
        └── UserApiTest.php
```

## Tipos de Testes

### 1. Testes Unitários (Unit Tests)

**Objetivo**: Testar componentes isolados sem dependências externas.

**Características:**
- ✅ Muito rápidos (< 1ms por teste)
- ✅ Sem banco de dados
- ✅ Usam mocks/stubs
- ✅ Testam lógica de negócio pura

**Exemplos:**

#### Value Objects
```php
// tests/Unit/Domain/User/ValueObjects/EmailTest.php
public function test_should_normalize_email_to_lowercase(): void
{
    $email = new Email('TEST@EXAMPLE.COM');

    $this->assertEquals('test@example.com', $email->value());
}
```

#### Entities
```php
// tests/Unit/Domain/User/Entities/UserTest.php
public function test_should_throw_exception_for_short_name(): void
{
    $this->expectException(\InvalidArgumentException::class);

    User::create('Jo', 'john@example.com', 'password123');
}
```

#### Use Cases (com Mocks)
```php
// tests/Unit/Application/User/UseCases/CreateUserUseCaseTest.php
public function test_should_create_user_successfully(): void
{
    $this->userRepository
        ->shouldReceive('save')
        ->once()
        ->with(Mockery::type(User::class));

    $output = $this->useCase->execute($dto);

    $this->assertEquals('John Doe', $output->name);
}
```

### 2. Testes de Integração (Integration Tests)

**Objetivo**: Testar integração com banco de dados e infraestrutura.

**Características:**
- ⚡ Rápidos (< 100ms por teste)
- 💾 Usa banco de dados real (SQLite em memória)
- 🔄 RefreshDatabase entre testes
- 🎯 Testa Repository + Eloquent

**Exemplo:**

```php
// tests/Integration/Infrastructure/Persistence/EloquentUserRepositoryTest.php
public function test_should_save_new_user(): void
{
    $user = User::create('John Doe', 'john@example.com', 'password123');

    $this->repository->save($user);

    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
}
```

### 3. Testes de Feature (Feature/E2E Tests)

**Objetivo**: Testar a aplicação completa (end-to-end).

**Características:**
- 🌐 Testa HTTP endpoints
- 📦 Stack completo (Controller → UseCase → Repository → DB)
- 💾 Usa banco de dados
- 🔍 Valida JSON responses

**Exemplo:**

```php
// tests/Feature/Api/UserApiTest.php
public function test_should_create_user_via_api(): void
{
    $response = $this->postJson('/api/v1/users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Usuário criado com sucesso',
        ]);
}
```

## Executando os Testes

### Todos os testes
```bash
php artisan test
```

### Apenas testes unitários
```bash
php artisan test --testsuite=Unit
```

### Apenas testes de integração
```bash
php artisan test tests/Integration
```

### Apenas testes de feature
```bash
php artisan test --testsuite=Feature
```

### Teste específico
```bash
php artisan test --filter=CreateUserUseCaseTest
```

### Com cobertura
```bash
php artisan test --coverage
```

### Com detalhes
```bash
php artisan test --verbose
```

## Cobertura de Testes

### Value Objects
- ✅ Email: 100% - 6 testes
- ✅ UserId: 100% - 5 testes

### Entities
- ✅ User: 100% - 8 testes

### Use Cases
- ✅ CreateUserUseCase: 100% - 3 testes
- ✅ UpdateUserUseCase: 100% - 3 testes
- ✅ DeleteUserUseCase: 100% - 2 testes
- ✅ GetUserUseCase: 100% - 2 testes
- ✅ ListUsersUseCase: 100% - 3 testes

### Repository
- ✅ EloquentUserRepository: 100% - 13 testes

### API
- ✅ User API: 100% - 13 testes

**Total: 58 testes**

## Pirâmide de Testes

```
        /\
       /  \      Feature (E2E)
      /____\     13 testes (~20%)
     /      \
    / Integration \
   /______________\   13 testes (~20%)
  /                \
 /      Unit        \
/____________________\  32 testes (~60%)
```

**Distribuição ideal:**
- 60% Unit (rápidos, isolados)
- 20% Integration (médios, com DB)
- 20% Feature (lentos, completos)

## Mocks vs Dados Reais

### Use Mocks quando:
- ✅ Testar lógica de negócio isolada
- ✅ Testar Use Cases
- ✅ Velocidade é importante
- ✅ Não precisa testar integração

### Use Dados Reais quando:
- ✅ Testar Repository
- ✅ Testar queries SQL
- ✅ Testar API completa
- ✅ Garantir integração funciona

## Boas Práticas

### ✅ Faça

1. **Um teste, uma asserção principal**
```php
public function test_should_create_valid_email(): void
{
    $email = new Email('test@example.com');

    $this->assertEquals('test@example.com', $email->value());
}
```

2. **Nomes descritivos**
```php
// ❌ Ruim
public function test_email(): void

// ✅ Bom
public function test_should_throw_exception_for_invalid_email(): void
```

3. **Arrange, Act, Assert (AAA)**
```php
public function test_should_update_user(): void
{
    // Arrange
    $user = User::create('Old Name', 'old@email.com', 'pass');

    // Act
    $user->updateProfile('New Name', 'new@email.com');

    // Assert
    $this->assertEquals('New Name', $user->name());
}
```

4. **Teste casos de erro**
```php
public function test_should_throw_exception_for_short_name(): void
{
    $this->expectException(\InvalidArgumentException::class);

    User::create('Jo', 'email@example.com', 'password');
}
```

5. **Use fixtures e factories quando necessário**
```php
private function createUser(string $name = 'John Doe'): User
{
    return User::create($name, 'john@example.com', 'password123');
}
```

### ❌ Não Faça

1. **Não teste frameworks**
```php
// ❌ Não faça
public function test_laravel_validation_works(): void
{
    // Laravel já testa isso
}
```

2. **Não acople testes**
```php
// ❌ Ruim - teste depende de outro
public function test_create_user(): void { /* ... */ }
public function test_update_created_user(): void { /* usa dados do teste anterior */ }
```

3. **Não use sleep() em testes**
```php
// ❌ Ruim
sleep(5);

// ✅ Melhor - use Mocks ou Carbon::setTestNow()
```

4. **Não ignore testes falhando**
```php
// ❌ Nunca faça
public function test_broken_feature(): void
{
    $this->markTestSkipped('TODO: fix later');
}
```

## TDD (Test-Driven Development)

### Ciclo Red-Green-Refactor

1. **🔴 Red**: Escreva um teste que falha
2. **🟢 Green**: Escreva código mínimo para passar
3. **♻️ Refactor**: Melhore o código mantendo testes passando

**Exemplo:**

```php
// 1. RED - Teste falha (classe não existe)
public function test_should_create_email(): void
{
    $email = new Email('test@example.com');
    $this->assertEquals('test@example.com', $email->value());
}

// 2. GREEN - Implementação mínima
class Email
{
    public function __construct(public string $value) {}
}

// 3. REFACTOR - Adiciona validação
class Email
{
    private string $value;

    public function __construct(string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Email inválido');
        }
        $this->value = $value;
    }

    public function value(): string { return $this->value; }
}
```

## CI/CD

### GitHub Actions exemplo

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v2

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2

      - name: Install dependencies
        run: composer install

      - name: Run tests
        run: php artisan test --coverage
```

## Debugging Testes

### Ver SQL queries
```php
\DB::enableQueryLog();
// ... código
dd(\DB::getQueryLog());
```

### Dump & Die em testes
```php
$response->dump(); // Mostra response
$response->dd();   // Dump e para
```

### Ver output de testes
```bash
php artisan test --verbose
```

## Resumo

| Tipo | Velocidade | Quando Usar | Exemplo |
|------|-----------|-------------|---------|
| **Unit** | ⚡⚡⚡ Muito rápido | Lógica de negócio | ValueObjects, Entities, Use Cases |
| **Integration** | ⚡⚡ Rápido | Integração com DB | Repository |
| **Feature** | ⚡ Médio | API completa | Endpoints HTTP |

**Meta**: 80%+ de cobertura, com foco em lógica de negócio crítica.

---

**Comandos úteis:**

```bash
# Rodar todos os testes
php artisan test

# Testes com cobertura
php artisan test --coverage --min=80

# Testes em paralelo (mais rápido)
php artisan test --parallel

# Apenas testes que falharam
php artisan test --failed

# Com profiling
php artisan test --profile
```
