# Laravel Clean Architecture + DDD

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-red?style=for-the-badge&logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php" alt="PHP">
  <img src="https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker" alt="Docker">
  <img src="https://img.shields.io/badge/Tests-58%20passing-success?style=for-the-badge" alt="Tests">
  <img src="https://img.shields.io/badge/API-Swagger%20Docs-85EA2D?style=for-the-badge&logo=swagger" alt="Swagger">
</p>

**Sistema de gerenciamento de usuários (CRUD) implementando Clean Architecture e Domain-Driven Design (DDD)**, demonstrando boas práticas de desenvolvimento de software com separação clara de responsabilidades, testabilidade e manutenibilidade.

## Índice

- [Características](#características)
- [Arquitetura](#arquitetura)
- [Tecnologias](#tecnologias)
- [Quick Start](#quick-start)
- [Documentação](#documentação)
- [API Endpoints](#api-endpoints)
- [Testes](#testes)
- [Princípios e Padrões](#princípios-e-padrões)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [Roadmap](#roadmap)

## Características

- Clean Architecture com separação em 4 camadas (Domain, Application, Infrastructure, Presentation)
- Domain-Driven Design com Entities, Value Objects e Domain Services
- DTOs (Data Transfer Objects) para transferência de dados entre camadas
- Repository Pattern com interfaces no Domain e implementação na Infrastructure
- Use Cases para orquestração de lógica de negócio
- 58 testes automatizados (Unit, Integration e Feature)
- Containerização completa com Docker e Docker Compose
- API RESTful com validação e tratamento de erros
- Documentação Swagger/OpenAPI interativa
- UUIDs como identificadores únicos
- Password hashing com bcrypt

## Arquitetura

O projeto segue os princípios de Clean Architecture, organizando o código em camadas concêntricas:

```
┌─────────────────────────────────────────┐
│         Presentation Layer (HTTP)       │  Controllers, Resources, Routes
├─────────────────────────────────────────┤
│       Application Layer (Use Cases)     │  CreateUser, UpdateUser, ListUsers
├─────────────────────────────────────────┤
│      Domain Layer (Business Logic)      │  Entities, Value Objects, Services
├─────────────────────────────────────────┤
│     Infrastructure Layer (Technical)    │  Eloquent, Repositories, Providers
└─────────────────────────────────────────┘
```

**Regra de Dependência**: As dependências apontam sempre para dentro. O Domain não conhece nada sobre Infrastructure ou HTTP.

## Tecnologias

| Tecnologia | Versão | Uso |
|------------|--------|-----|
| **Laravel** | 12.x | Framework PHP |
| **PHP** | 8.2 | Linguagem |
| **MySQL** | 8.0 | Banco de dados |
| **Docker** | Latest | Containerização |
| **PHPUnit** | 11.x | Testes |
| **Swagger/OpenAPI** | 3.0 | Documentação API |
| **Nginx** | Latest | Web server |
| **phpMyAdmin** | Latest | Interface MySQL |

## Quick Start

### Pré-requisitos

- Docker Desktop instalado
- Docker Compose instalado

### 1. Clone o repositório

```bash
git clone https://github.com/matheuspdias/laravel-clean-arch.git
cd laravel-clean-arch
```

### 2. Subir o ambiente Docker

```bash
docker compose up -d --build
```

### 3. Instalar dependências

```bash
docker compose exec app composer install
```

### 4. Configurar o ambiente

```bash
docker compose exec app php artisan key:generate
```

### 5. Rodar migrations

```bash
docker compose exec app php artisan migrate
```

### 6. Gerar documentação Swagger

```bash
docker compose exec app php artisan l5-swagger:generate
```

### 7. Testar a API

```bash
curl http://localhost:8000/api/v1/users
```

**Pronto!** A aplicação está rodando em `http://localhost:8000`

### Acessar Documentação Swagger

Acesse a documentação interativa da API em: **http://localhost:8000/api/documentation**

## Documentação

O projeto possui documentação detalhada para cada aspecto:

| Documento | Descrição |
|-----------|-----------|
| [API_DOCUMENTATION.md](API_DOCUMENTATION.md) | Documentação completa da API REST, endpoints, requests, responses e exemplos |
| [SWAGGER_DOCUMENTATION.md](SWAGGER_DOCUMENTATION.md) | Guia da documentação Swagger/OpenAPI interativa e como usá-la |
| [DOCKER.md](DOCKER.md) | Guia completo do ambiente Docker: serviços, comandos úteis e troubleshooting |
| [DTOS_DOCUMENTATION.md](DTOS_DOCUMENTATION.md) | Explicação sobre DTOs: o que são, por que usar e exemplos práticos |
| [TESTS_DOCUMENTATION.md](TESTS_DOCUMENTATION.md) | Guia de testes: estrutura, tipos, cobertura, TDD e boas práticas |

## API Endpoints

**Base URL**: `http://localhost:8000/api/v1`

**Documentação Interativa**: [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)

| Método | Endpoint | Descrição | Auth |
|--------|----------|-----------|------|
| GET | `/users` | Listar usuários (paginado) | - |
| GET | `/users/{id}` | Buscar usuário por ID | - |
| POST | `/users` | Criar novo usuário | - |
| PUT | `/users/{id}` | Atualizar usuário | - |
| DELETE | `/users/{id}` | Deletar usuário | - |

### Exemplo de Requisição

```bash
curl -X POST http://localhost:8000/api/v1/users \
  -H "Content-Type: application/json" \
  -d '{
    "name": "João Silva",
    "email": "joao@example.com",
    "password": "senha123"
  }'
```

### Exemplo de Resposta

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

### Documentação Interativa (Swagger)

Você também pode testar todos os endpoints através da **interface interativa Swagger**:

1. Acesse: http://localhost:8000/api/documentation
2. Escolha um endpoint (ex: POST /api/v1/users)
3. Clique em "Try it out"
4. Preencha os dados no formulário
5. Clique em "Execute"
6. Veja a resposta em tempo real

Ver [API_DOCUMENTATION.md](API_DOCUMENTATION.md) para mais detalhes ou [SWAGGER_DOCUMENTATION.md](SWAGGER_DOCUMENTATION.md) para guia completo do Swagger.

## Testes

O projeto possui **58 testes automatizados** distribuídos em 3 categorias:

```
tests/
├── Unit/                    # 32 testes - Lógica de negócio isolada
│   ├── Domain/
│   │   ├── Entities/       # 8 testes
│   │   └── ValueObjects/   # 11 testes
│   └── Application/
│       └── UseCases/       # 13 testes
│
├── Integration/             # 13 testes - Integração com banco
│   └── Infrastructure/
│       └── Persistence/
│
└── Feature/                 # 13 testes - API end-to-end
    └── Api/
```

### Executar Testes

```bash
# Todos os testes
docker compose exec app php artisan test

# Apenas testes unitários
docker compose exec app php artisan test --testsuite=Unit

# Com cobertura de código
docker compose exec app php artisan test --coverage

# Testes em paralelo
docker compose exec app php artisan test --parallel
```

### Cobertura de Testes

- **Value Objects**: 100% - 11 testes
- **Entities**: 100% - 8 testes
- **Use Cases**: 100% - 13 testes
- **Repository**: 100% - 13 testes
- **API**: 100% - 13 testes

Ver [TESTS_DOCUMENTATION.md](TESTS_DOCUMENTATION.md) para guia completo.

## Princípios e Padrões

### Clean Architecture

- **Separação de Responsabilidades**: Cada camada tem sua responsabilidade bem definida
- **Independência de Frameworks**: Domain não conhece Laravel
- **Testabilidade**: Lógica de negócio totalmente testável
- **Independência de UI**: Use Cases podem ser usados em CLI, API, GraphQL, etc.
- **Independência de Banco**: Repository abstrai completamente a persistência

### Domain-Driven Design (DDD)

- **Entities**: `User` - Objetos com identidade e ciclo de vida
- **Value Objects**: `UserId`, `Email` - Objetos imutáveis sem identidade própria
- **Repositories**: Interfaces no Domain, implementação na Infrastructure
- **Domain Services**: `UserDomainService` - Lógica que não pertence a uma entidade específica
- **Use Cases**: Orquestração das operações de negócio

### SOLID Principles

| Princípio | Aplicação no Projeto |
|-----------|---------------------|
| **SRP** | Cada classe tem uma única responsabilidade |
| **OCP** | Aberto para extensão (interfaces), fechado para modificação |
| **LSP** | Substituição por interfaces (UserRepository) |
| **ISP** | Interfaces específicas e segregadas |
| **DIP** | Dependência de abstrações, não de implementações concretas |

### DTOs (Data Transfer Objects)

DTOs garantem **type safety** e **desacoplamento** entre as camadas:

- **Request DTOs**: `CreateUserDTO`, `UpdateUserDTO`, `ListUsersDTO`
- **Response DTOs**: `UserDTO`, `UserListDTO`

Ver [DTOS_DOCUMENTATION.md](DTOS_DOCUMENTATION.md) para mais informações.

## Estrutura do Projeto

```
app/
├── Domain/                          # Camada de Domínio (regras de negócio puras)
│   └── User/
│       ├── Entities/               # User.php
│       ├── ValueObjects/           # UserId.php, Email.php
│       ├── Repositories/           # UserRepository.php (interface)
│       └── Services/               # UserDomainService.php
│
├── Application/                     # Camada de Aplicação (casos de uso)
│   └── User/
│       ├── DTOs/
│       │   ├── Request/            # CreateUserDTO, UpdateUserDTO, etc.
│       │   └── Response/           # UserDTO, UserListDTO
│       └── UseCases/
│           ├── CreateUserUseCase.php
│           ├── UpdateUserUseCase.php
│           ├── DeleteUserUseCase.php
│           ├── GetUserUseCase.php
│           └── ListUsersUseCase.php
│
├── Infrastructure/                  # Camada de Infraestrutura (implementações técnicas)
│   ├── Persistence/
│   │   └── Eloquent/
│   │       ├── UserModel.php
│   │       └── EloquentUserRepository.php
│   └── Providers/
│       └── RepositoryServiceProvider.php
│
└── Http/                           # Camada de Apresentação (interface HTTP)
    ├── Controllers/
    │   └── Api/
    │       └── UserController.php
    └── Resources/
        └── UserResource.php
```

## Regras de Negócio

- Nome deve ter **no mínimo 3 caracteres**
- Email deve ser **válido e único** no sistema
- Senha é **hasheada automaticamente** usando bcrypt
- IDs são **UUIDs v4** gerados automaticamente
- Email é **normalizado para lowercase**

## Docker Services

O ambiente Docker possui 4 serviços:

| Serviço | Descrição | Porta |
|---------|-----------|-------|
| **app** | PHP 8.2-FPM com Laravel | - |
| **nginx** | Servidor web | 8000 |
| **mysql** | MySQL 8.0 | 3306 |
| **phpmyadmin** | Interface web MySQL | 8080 |

### Acessos

- **Aplicação**: http://localhost:8000
- **Documentação API (Swagger)**: http://localhost:8000/api/documentation
- **phpMyAdmin**: http://localhost:8080
  - Usuário: `root`
  - Senha: `root`

Ver [DOCKER.md](DOCKER.md) para comandos úteis e troubleshooting.

## Comandos Úteis

### Docker

```bash
# Entrar no container
docker compose exec app bash

# Ver logs
docker compose logs -f app

# Parar containers
docker compose down

# Rebuild completo
docker compose down -v && docker compose build --no-cache && docker compose up -d
```

### Laravel Artisan

```bash
# Rodar migrations
docker compose exec app php artisan migrate

# Reverter migrations
docker compose exec app php artisan migrate:rollback

# Gerar documentação Swagger
docker compose exec app php artisan l5-swagger:generate

# Limpar caches
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan view:clear

# Rodar testes
docker compose exec app php artisan test
```

## Roadmap

### Implementado

- [x] Clean Architecture com 4 camadas
- [x] DDD com Entities, Value Objects e Domain Services
- [x] DTOs para todas as operações
- [x] Repository Pattern
- [x] 58 testes automatizados (100% cobertura em camadas críticas)
- [x] Containerização completa com Docker
- [x] API RESTful documentada

### Próximos Passos

- [ ] Autenticação JWT
- [ ] Eventos de Domínio (Domain Events)
- [ ] Observabilidade (Logs estruturados, Métricas)
- [ ] Cache (Redis)
- [ ] Validação de CPF como Value Object
- [ ] Soft Deletes
- [ ] GraphQL API
- [ ] Command Bus Pattern
- [ ] Event Sourcing
- [ ] CQRS (Command Query Responsibility Segregation)

## Contribuindo

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## Licença

Este projeto é licenciado sob a [MIT License](https://opensource.org/licenses/MIT).

## Contato

Para dúvidas ou sugestões, abra uma issue no repositório.

---

**Desenvolvido com Clean Architecture + DDD**
