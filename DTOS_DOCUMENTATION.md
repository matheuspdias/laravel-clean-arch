# DTOs (Data Transfer Objects) - Documentação

## O que são DTOs?

DTOs (Data Transfer Objects) são objetos simples que servem para **transferir dados entre camadas** da aplicação. Eles não contêm lógica de negócio, apenas dados.

## Por que usar DTOs?

### ✅ Vantagens

1. **Desacoplamento**: Separa a representação de dados da lógica de negócio
2. **Type Safety**: Garante tipagem forte em toda a aplicação
3. **Validação**: Ponto único para transformação e validação de dados
4. **Contrato Claro**: Define exatamente quais dados entram e saem dos Use Cases
5. **Facilita Testes**: Criação de objetos de teste mais simples
6. **Documentação Viva**: O código documenta o que é esperado
7. **Refatoração Segura**: Mudanças em uma camada não afetam outras

## Estrutura no Projeto

```
app/Application/User/DTOs/
├── Request/                    # DTOs de entrada
│   ├── CreateUserDTO.php
│   ├── UpdateUserDTO.php
│   └── ListUsersDTO.php
└── Response/                   # DTOs de saída
    ├── UserDTO.php
    └── UserListDTO.php
```

## Tipos de DTOs

### 1. Request DTOs (Entrada)

Recebem dados da camada HTTP e os transformam para os Use Cases.

**Exemplo: CreateUserDTO**

```php
class CreateUserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password']
        );
    }
}
```

**Características:**
- Imutáveis (`readonly`)
- Factory method `fromArray()` para criação a partir de arrays
- Validação de estrutura (não de regras de negócio)

### 2. Response DTOs (Saída)

Retornam dados dos Use Cases para a camada HTTP.

**Exemplo: UserDTO**

```php
class UserDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $email,
        public readonly DateTimeImmutable $createdAt,
        public readonly DateTimeImmutable $updatedAt
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
```

**Características:**
- Método `toArray()` para serialização
- Dados formatados para apresentação
- Não expõe informações sensíveis (ex: password)

## Fluxo de Dados com DTOs

```
┌─────────────┐
│   Request   │  (HTTP Layer)
└──────┬──────┘
       │ fromArray()
       ▼
┌─────────────────┐
│  Request DTO    │  (Application Layer)
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│   Use Case      │  (Application Layer)
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│  Response DTO   │  (Application Layer)
└──────┬──────────┘
       │ toArray()
       ▼
┌─────────────────┐
│   Response      │  (HTTP Layer)
└─────────────────┘
```

## Exemplo Completo: Criar Usuário

### 1. Controller (HTTP Layer)

```php
public function store(Request $request, CreateUserUseCase $useCase): JsonResponse
{
    // Validação do Laravel
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|min:3',
        'email' => 'required|email',
        'password' => 'required|string|min:6',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Cria DTO de Request
    $dto = CreateUserDTO::fromArray($request->all());

    // Executa Use Case
    $output = $useCase->execute($dto);

    // Retorna DTO de Response
    return response()->json([
        'message' => 'Usuário criado',
        'data' => UserResource::make($output->toArray())->resolve()
    ], 201);
}
```

### 2. Use Case (Application Layer)

```php
public function execute(CreateUserDTO $dto): UserDTO
{
    // Validação de domínio
    $email = new Email($dto->email);
    $this->userDomainService->ensureEmailIsUnique($email);

    // Criação da entidade
    $user = User::create($dto->name, $dto->email, $dto->password);

    // Persistência
    $this->userRepository->save($user);

    // Retorna DTO de Response
    return new UserDTO(
        $user->id()->value(),
        $user->name(),
        $user->email()->value(),
        $user->createdAt(),
        $user->updatedAt()
    );
}
```

## Boas Práticas

### ✅ Faça

1. **Use DTOs imutáveis** (`readonly` properties)
2. **Crie factory methods** (`fromArray`, `fromRequest`, etc.)
3. **Um DTO por Use Case** (entrada e saída)
4. **Métodos de conversão** (`toArray()`, `toJson()`)
5. **Validação básica** no factory method
6. **Documente os campos** com PHPDoc quando necessário

### ❌ Não Faça

1. **Não adicione lógica de negócio** nos DTOs
2. **Não acesse banco de dados** nos DTOs
3. **Não faça DTOs mutáveis**
4. **Não reutilize DTOs entre contextos diferentes**
5. **Não exponha dados sensíveis** nos Response DTOs

## DTOs vs Entities vs Value Objects

| Aspecto | DTO | Entity | Value Object |
|---------|-----|--------|--------------|
| **Propósito** | Transferir dados | Identidade + Comportamento | Valor sem identidade |
| **Lógica** | Nenhuma | Regras de negócio | Validação + Comparação |
| **Mutabilidade** | Imutável | Mutável | Imutável |
| **Identidade** | Não | Sim (ID) | Não |
| **Camada** | Application | Domain | Domain |
| **Exemplo** | CreateUserDTO | User | Email, UserId |

## Exemplo Prático: ListUsersDTO

### Request DTO

```php
class ListUsersDTO
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 15
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            page: (int) ($data['page'] ?? 1),
            perPage: (int) ($data['per_page'] ?? 15)
        );
    }
}
```

### Response DTO

```php
class UserListDTO
{
    /**
     * @param UserDTO[] $users
     */
    public function __construct(
        public readonly array $users,
        public readonly int $total,
        public readonly int $page,
        public readonly int $perPage
    ) {}

    public function totalPages(): int
    {
        return (int) ceil($this->total / $this->perPage);
    }

    public function toArray(): array
    {
        return [
            'users' => array_map(fn($user) => $user->toArray(), $this->users),
            'total' => $this->total,
            'page' => $this->page,
            'per_page' => $this->perPage,
            'total_pages' => $this->totalPages(),
        ];
    }
}
```

## Quando NÃO usar DTOs?

1. **Comunicação interna entre objetos da mesma camada**
   - Use diretamente as entities ou value objects

2. **Operações muito simples**
   - Se é só passar 1 ou 2 parâmetros primitivos, não precisa

3. **Performance crítica**
   - Em loops muito grandes, a criação de objetos pode ter overhead

## Evolução e Manutenção

### Adicionando um campo

```php
// Antes
class CreateUserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password
    ) {}
}

// Depois (com campo opcional)
class CreateUserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $phone = null  // Novo campo opcional
    ) {}
}
```

### Versionamento de DTOs

Para quebras de compatibilidade, crie novas versões:

```php
// app/Application/User/DTOs/Request/V2/CreateUserDTO.php
namespace App\Application\User\DTOs\Request\V2;

class CreateUserDTO
{
    // Nova estrutura
}
```

## Resumo

Os DTOs são uma **excelente prática** em Clean Architecture porque:

1. ✅ **Separam as camadas** - Application não conhece detalhes do HTTP
2. ✅ **Type Safety** - Erros detectados em tempo de desenvolvimento
3. ✅ **Documentação** - Código auto-explicativo
4. ✅ **Testabilidade** - Fácil criar dados de teste
5. ✅ **Manutenibilidade** - Mudanças localizadas

---

**Conclusão:** Use DTOs sempre que precisar transferir dados entre camadas diferentes da aplicação. Eles são o "contrato" entre as camadas e garantem que a comunicação seja clara e type-safe.
