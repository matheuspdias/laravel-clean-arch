# Laravel Clean Architecture + DDD - CRUD de Usuários

CRUD completo de usuários implementado com Clean Architecture e Domain-Driven Design.

## Tecnologias

- Laravel 12
- PHP 8.2
- MySQL 8.0
- Docker & Docker Compose
- Clean Architecture
- Domain-Driven Design (DDD)

## Estrutura do Projeto

```
app/
├── Domain/                          # Regras de negócio puras
│   └── User/
│       ├── Entities/               # User
│       ├── ValueObjects/           # UserId, Email
│       ├── Repositories/           # Interfaces
│       └── Services/               # Domain Services
│
├── Application/                     # Casos de uso
│   └── User/
│       ├── DTOs/                   # Data Transfer Objects
│       │   ├── Request/            # DTOs de entrada
│       │   └── Response/           # DTOs de saída
│       └── UseCases/
│           ├── CreateUserUseCase.php
│           ├── UpdateUserUseCase.php
│           ├── DeleteUserUseCase.php
│           ├── GetUserUseCase.php
│           └── ListUsersUseCase.php
│
├── Infrastructure/                  # Implementações técnicas
│   ├── Persistence/Eloquent/
│   └── Providers/
│
└── Http/                           # Controllers e Resources
    ├── Controllers/Api/
    └── Resources/
```

## Quick Start

### 1. Subir o ambiente Docker

```bash
docker compose up -d --build
```

### 2. Instalar dependências

```bash
docker compose exec app composer install
```

### 3. Rodar migrations

```bash
docker compose exec app php artisan migrate
```

### 4. Testar a API

```bash
# Opção 1: Script automatizado
./test-api.sh

# Opção 2: Manual
curl http://localhost:8000/api/v1/users
```

## Endpoints

- `GET /api/v1/users` - Listar usuários
- `GET /api/v1/users/{id}` - Buscar por ID
- `POST /api/v1/users` - Criar usuário
- `PUT /api/v1/users/{id}` - Atualizar usuário
- `DELETE /api/v1/users/{id}` - Deletar usuário

📖 **[Documentação Completa da API](API_DOCUMENTATION.md)**

## Acessos

- **API**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080 (root/root)

## Comandos Úteis

```bash
# Entrar no container
docker compose exec app bash

# Rodar migrations
docker compose exec app php artisan migrate

# Reverter migrations
docker compose exec app php artisan migrate:rollback

# Limpar cache
docker compose exec app php artisan cache:clear

# Ver logs
docker compose logs -f app
```

## Princípios Implementados

### Clean Architecture
- ✅ Separação em camadas (Domain, Application, Infrastructure, Http)
- ✅ Regra de dependência (sempre para dentro)
- ✅ Independência de frameworks
- ✅ Testabilidade

### DDD
- ✅ Entities (User)
- ✅ Value Objects (UserId, Email)
- ✅ Repositories (interface + implementação)
- ✅ Domain Services (validações de negócio)
- ✅ Use Cases (orquestração)

### SOLID
- ✅ Single Responsibility
- ✅ Open/Closed
- ✅ Liskov Substitution
- ✅ Interface Segregation
- ✅ Dependency Inversion

### DTOs (Data Transfer Objects)
- ✅ Request DTOs (entrada dos Use Cases)
- ✅ Response DTOs (saída dos Use Cases)
- ✅ Desacoplamento entre camadas
- ✅ Type Safety em toda aplicação

## Exemplo de Uso

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
```

## Arquivos de Documentação

- 📄 [DOCKER.md](DOCKER.md) - Documentação do Docker
- 📄 [API_DOCUMENTATION.md](API_DOCUMENTATION.md) - Documentação completa da API
- 📄 [DTOS_DOCUMENTATION.md](DTOS_DOCUMENTATION.md) - Documentação sobre DTOs
- 📄 [TESTS_DOCUMENTATION.md](TESTS_DOCUMENTATION.md) - Documentação de testes
- 📄 [test-api.sh](test-api.sh) - Script de teste automatizado

## Regras de Negócio

- Nome deve ter no mínimo 3 caracteres
- Email deve ser válido e único
- Senha é hasheada automaticamente (bcrypt)
- IDs são UUIDs v4

## Testes

### Estrutura de Testes
```
tests/
├── Unit/                    # Testes unitários (32 testes)
│   ├── Domain/
│   └── Application/
├── Integration/             # Testes de integração (13 testes)
│   └── Infrastructure/
└── Feature/                 # Testes E2E (13 testes)
    └── Api/
```

### Executar Testes

```bash
# Todos os testes (58 testes)
docker compose exec app php artisan test

# Apenas unitários
docker compose exec app php artisan test --testsuite=Unit

# Com cobertura
docker compose exec app php artisan test --coverage
```

📖 **[Documentação Completa de Testes](TESTS_DOCUMENTATION.md)**

## Próximos Passos

- [x] ✅ Adicionar DTOs
- [x] ✅ Adicionar testes unitários
- [x] ✅ Adicionar testes de integração
- [x] ✅ Adicionar testes de feature
- [ ] Implementar autenticação JWT
- [ ] Implementar eventos de domínio
- [ ] Adicionar observabilidade (logs, métricas)
- [ ] Implementar cache
- [ ] Adicionar validação de CPF (ValueObject)
- [ ] Implementar soft deletes

---

**Desenvolvido com Clean Architecture + DDD** 🏗️
