# API de Usuários - CRUD com Clean Architecture e DDD

## Arquitetura

Este projeto implementa um CRUD de usuários seguindo os princípios de Clean Architecture e Domain-Driven Design (DDD).

### Estrutura de Diretórios

```
app/
├── Domain/                          # Camada de Domínio
│   └── User/
│       ├── Entities/               # Entidades de negócio
│       │   └── User.php
│       ├── ValueObjects/           # Objetos de valor
│       │   ├── UserId.php
│       │   └── Email.php
│       ├── Repositories/           # Interfaces dos repositórios
│       │   └── UserRepository.php
│       └── Services/               # Serviços de domínio
│           └── UserDomainService.php
│
├── Application/                     # Camada de Aplicação
│   └── User/
│       └── UseCases/               # Casos de uso
│           ├── CreateUserUseCase.php
│           ├── UpdateUserUseCase.php
│           ├── DeleteUserUseCase.php
│           ├── GetUserUseCase.php
│           └── ListUsersUseCase.php
│
├── Infrastructure/                  # Camada de Infraestrutura
│   ├── Persistence/
│   │   └── Eloquent/
│   │       ├── UserModel.php
│   │       └── EloquentUserRepository.php
│   └── Providers/
│       └── RepositoryServiceProvider.php
│
└── Http/                           # Camada de Apresentação
    ├── Controllers/
    │   └── Api/
    │       └── UserController.php
    └── Resources/
        └── UserResource.php
```

## Endpoints da API

Base URL: `http://localhost:8000/api/v1`

### 1. Listar Usuários

```http
GET /api/v1/users?page=1&per_page=15
```

**Resposta:**
```json
{
  "data": [
    {
      "id": "9d7c1a2b-3e4f-5a6b-7c8d-9e0f1a2b3c4d",
      "name": "João Silva",
      "email": "joao@example.com",
      "created_at": "2025-11-13 20:00:00",
      "updated_at": "2025-11-13 20:00:00"
    }
  ],
  "meta": {
    "total": 50,
    "page": 1,
    "per_page": 15,
    "total_pages": 4
  }
}
```

### 2. Buscar Usuário por ID

```http
GET /api/v1/users/{id}
```

**Resposta:**
```json
{
  "data": {
    "id": "9d7c1a2b-3e4f-5a6b-7c8d-9e0f1a2b3c4d",
    "name": "João Silva",
    "email": "joao@example.com",
    "created_at": "2025-11-13 20:00:00",
    "updated_at": "2025-11-13 20:00:00"
  }
}
```

### 3. Criar Usuário

```http
POST /api/v1/users
Content-Type: application/json
```

**Body:**
```json
{
  "name": "João Silva",
  "email": "joao@example.com",
  "password": "senha123"
}
```

**Validações:**
- `name`: obrigatório, mínimo 3 caracteres, máximo 255
- `email`: obrigatório, deve ser válido, máximo 255
- `password`: obrigatório, mínimo 6 caracteres

**Resposta (201):**
```json
{
  "message": "Usuário criado com sucesso",
  "data": {
    "id": "9d7c1a2b-3e4f-5a6b-7c8d-9e0f1a2b3c4d",
    "name": "João Silva",
    "email": "joao@example.com",
    "created_at": "2025-11-13 20:00:00",
    "updated_at": "2025-11-13 20:00:00"
  }
}
```

### 4. Atualizar Usuário

```http
PUT /api/v1/users/{id}
Content-Type: application/json
```

**Body:**
```json
{
  "name": "João Silva Santos",
  "email": "joao.santos@example.com"
}
```

**Resposta:**
```json
{
  "message": "Usuário atualizado com sucesso",
  "data": {
    "id": "9d7c1a2b-3e4f-5a6b-7c8d-9e0f1a2b3c4d",
    "name": "João Silva Santos",
    "email": "joao.santos@example.com",
    "created_at": "2025-11-13 20:00:00",
    "updated_at": "2025-11-13 20:30:00"
  }
}
```

### 5. Deletar Usuário

```http
DELETE /api/v1/users/{id}
```

**Resposta:**
```json
{
  "message": "Usuário deletado com sucesso"
}
```

## Erros

### 422 - Validation Error
```json
{
  "message": "Validation failed",
  "errors": {
    "email": ["Email inválido"],
    "name": ["Nome deve ter no mínimo 3 caracteres"]
  }
}
```

### 422 - Domain Error
```json
{
  "message": "Email já está em uso"
}
```

### 404 - Not Found
```json
{
  "message": "Usuário não encontrado"
}
```

### 500 - Server Error
```json
{
  "message": "Erro ao criar usuário",
  "error": "Detalhes do erro"
}
```

## Regras de Negócio (Domain)

### User Entity
- Nome deve ter no mínimo 3 caracteres
- Email deve ser válido e único
- Senha é hasheada automaticamente com bcrypt

### Value Objects
- **UserId**: UUID v4 gerado automaticamente
- **Email**: Validado e normalizado (lowercase)

### Domain Services
- `UserDomainService`: Garante unicidade de email

## Testando a API

### Com curl:

```bash
# Criar usuário
curl -X POST http://localhost:8000/api/v1/users \
  -H "Content-Type: application/json" \
  -d '{
    "name": "João Silva",
    "email": "joao@example.com",
    "password": "senha123"
  }'

# Listar usuários
curl http://localhost:8000/api/v1/users

# Buscar usuário
curl http://localhost:8000/api/v1/users/{id}

# Atualizar usuário
curl -X PUT http://localhost:8000/api/v1/users/{id} \
  -H "Content-Type: application/json" \
  -d '{
    "name": "João Silva Santos",
    "email": "joao.santos@example.com"
  }'

# Deletar usuário
curl -X DELETE http://localhost:8000/api/v1/users/{id}
```

## Instalação e Configuração

### 1. Instalar dependências
```bash
docker compose exec app composer install
```

### 2. Rodar migrations
```bash
docker compose exec app php artisan migrate
```

### 3. Testar a API
```bash
curl http://localhost:8000/api/v1/users
```

## Princípios Aplicados

### Clean Architecture
- **Independência de frameworks**: Domain não conhece Laravel
- **Testável**: Lógica de negócio isolada
- **Independência de UI**: Use cases podem ser usados em CLI, API, etc.
- **Independência de DB**: Repository abstrai persistência

### DDD
- **Entities**: User com identidade e ciclo de vida
- **Value Objects**: UserId, Email (imutáveis, sem identidade)
- **Repositories**: Interface no Domain, implementação na Infrastructure
- **Domain Services**: Lógica que não pertence a uma entidade
- **Use Cases**: Orquestração das operações

### SOLID
- **SRP**: Cada classe tem uma responsabilidade única
- **OCP**: Aberto para extensão, fechado para modificação
- **LSP**: Interfaces bem definidas
- **ISP**: Interfaces específicas
- **DIP**: Dependência de abstrações (interfaces)
