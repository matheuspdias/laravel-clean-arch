# Docker Setup - Laravel Clean Architecture

## Serviços Disponíveis

- **app**: PHP 8.2-FPM com Laravel
- **nginx**: Servidor web (porta 8000)
- **mysql**: MySQL 8.0 (porta 3306)
- **phpmyadmin**: Interface web para MySQL (porta 8080)

## Como usar

### 1. Build dos containers
```bash
docker compose build
```

### 2. Subir os containers
```bash
docker compose up -d
```

### 3. Instalar dependências
```bash
docker compose exec app composer install
```

### 4. Gerar chave da aplicação (se necessário)
```bash
docker compose exec app php artisan key:generate
```

### 5. Rodar migrations
```bash
docker compose exec app php artisan migrate
```

## Acessos

- **Aplicação**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080
  - Usuário: `root`
  - Senha: `root`

## Credenciais do Banco de Dados

- Host: `mysql` (dentro do container) ou `localhost` (fora do container)
- Porta: `3306`
- Database: `laravel_clean_arch`
- Usuário: `laravel`
- Senha: `password`
- Root Password: `root`

## Comandos Úteis

### Entrar no container
```bash
docker compose exec app bash
```

### Ver logs
```bash
docker compose logs -f app
```

### Parar containers
```bash
docker compose down
```

### Parar e remover volumes
```bash
docker compose down -v
```

### Rodar comandos artisan
```bash
docker compose exec app php artisan [comando]
```

### Rodar testes
```bash
docker compose exec app php artisan test
```

### Limpar cache
```bash
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan view:clear
```

## Estrutura de Arquivos Docker

```
.
├── docker compose.yml
├── Dockerfile
└── docker/
    ├── nginx/
    │   └── nginx.conf
    ├── php/
    │   └── local.ini
    └── mysql/
        └── my.cnf
```

## Troubleshooting

### Permissões
Se tiver problemas de permissão, execute:
```bash
docker compose exec app chown -R laravel:laravel /var/www/storage
docker compose exec app chown -R laravel:laravel /var/www/bootstrap/cache
```

### Rebuild completo
```bash
docker compose down -v
docker compose build --no-cache
docker compose up -d
```
