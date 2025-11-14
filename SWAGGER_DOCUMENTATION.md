# Documentação Swagger/OpenAPI

## O que é Swagger?

Swagger (OpenAPI) é uma especificação padrão para documentação de APIs RESTful. Ele fornece uma interface interativa onde você pode:

- Visualizar todos os endpoints disponíveis
- Ver a estrutura de requests e responses
- Testar endpoints diretamente no navegador
- Gerar código client automaticamente
- Exportar especificação OpenAPI

## Acessando a Documentação

A documentação interativa está disponível em:

**URL**: [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)

## Características da Documentação

### Interface Interativa

A documentação Swagger permite:

1. **Explorar Endpoints**: Navegar por todos os endpoints disponíveis organizados por tags
2. **Ver Detalhes**: Visualizar parâmetros, headers, body e responses esperados
3. **Testar API**: Executar requisições diretamente pela interface
4. **Ver Exemplos**: Exemplos de requests e responses para cada endpoint
5. **Validação**: Campos obrigatórios e tipos de dados claramente marcados

### Endpoints Documentados

Todos os endpoints da API de Usuários estão documentados:

- **GET /api/v1/users** - Listar usuários (com paginação)
- **GET /api/v1/users/{id}** - Buscar usuário por ID
- **POST /api/v1/users** - Criar novo usuário
- **PUT /api/v1/users/{id}** - Atualizar usuário
- **DELETE /api/v1/users/{id}** - Deletar usuário

## Estrutura da Documentação

### Organização com Traits

Para manter o código limpo e organizado, a documentação Swagger foi implementada usando **Traits**:

```
app/Http/Controllers/Api/Swagger/
├── SwaggerInfo.php                    # Informações gerais da API
└── UserSwaggerDocumentation.php       # Documentação dos endpoints de usuários
```

#### SwaggerInfo.php

Contém as informações gerais da API:

- Título e descrição
- Versão da API
- Informações de contato
- Servidores disponíveis (local e produção)

#### UserSwaggerDocumentation.php

Contém a documentação de todos os endpoints de usuários:

- Descrições detalhadas
- Parâmetros de entrada
- Estrutura de requests
- Estrutura de responses
- Códigos de status HTTP
- Exemplos de dados

### Como as Traits são Usadas

No `UserController.php`:

```php
use App\Http\Controllers\Api\Swagger\SwaggerInfo;
use App\Http\Controllers\Api\Swagger\UserSwaggerDocumentation;

class UserController extends Controller
{
    use SwaggerInfo;
    use UserSwaggerDocumentation;

    // ... métodos do controller
}
```

## Usando a Interface Swagger

### 1. Navegar pelos Endpoints

- Acesse http://localhost:8000/api/documentation
- Clique em qualquer endpoint para expandir detalhes
- Veja parâmetros, body e responses

### 2. Testar um Endpoint

#### Exemplo: Criar Usuário

1. Expanda o endpoint **POST /api/v1/users**
2. Clique no botão **"Try it out"**
3. Edite o JSON no campo "Request body":

```json
{
  "name": "João Silva",
  "email": "joao@example.com",
  "password": "senha123"
}
```

4. Clique em **"Execute"**
5. Veja a resposta abaixo, incluindo:
   - Status code (201)
   - Response body
   - Headers

#### Exemplo: Listar Usuários

1. Expanda o endpoint **GET /api/v1/users**
2. Clique em **"Try it out"**
3. Configure os parâmetros opcionais:
   - `page`: 1
   - `per_page`: 15
4. Clique em **"Execute"**
5. Veja a lista de usuários retornada

### 3. Ver Schemas

- Role até o final da página
- Encontre a seção **"Schemas"**
- Veja os modelos de dados usados pela API

## Gerando a Documentação

A documentação Swagger é gerada automaticamente a partir das annotations no código.

### Gerar/Atualizar Documentação

```bash
docker compose exec app php artisan l5-swagger:generate
```

Este comando deve ser executado:

- Após adicionar novos endpoints
- Após modificar a documentação existente
- Após alterar schemas de request/response

### Limpar Cache da Documentação

```bash
docker compose exec app php artisan cache:clear
```

## Annotations OpenAPI

A documentação usa PHP Attributes (OpenAPI Annotations):

### Exemplo de Endpoint Documentado

```php
#[OA\Post(
    path: '/api/v1/users',
    summary: 'Criar novo usuário',
    description: 'Cria um novo usuário no sistema',
    tags: ['Usuários'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['name', 'email', 'password'],
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'João Silva'),
                new OA\Property(property: 'email', type: 'string', example: 'joao@example.com'),
                new OA\Property(property: 'password', type: 'string', example: 'senha123')
            ]
        )
    ),
    responses: [
        new OA\Response(
            response: 201,
            description: 'Usuário criado com sucesso',
            content: new OA\JsonContent(...)
        )
    ]
)]
public function store() {}
```

## Exportando a Especificação

### JSON

A especificação OpenAPI em JSON está disponível em:

```
http://localhost:8000/api/documentation.json
```

### YAML

Para obter em formato YAML, configure em `config/l5-swagger.php`:

```php
'format' => 'yaml',
```

Depois gere novamente:

```bash
docker compose exec app php artisan l5-swagger:generate
```

## Customização

### Configuração

O arquivo de configuração está em:

```
config/l5-swagger.php
```

Principais configurações:

- **Rota da documentação**: `api/documentation` (padrão)
- **Título**: Configurado no SwaggerInfo trait
- **Servidores**: Definidos no SwaggerInfo trait
- **Segurança**: Configurar authentication schemes

### Temas

Para mudar o tema da interface Swagger UI, edite o arquivo:

```
resources/views/vendor/l5-swagger/index.blade.php
```

## Adicionando Novos Endpoints

### 1. Criar Trait de Documentação

```php
// app/Http/Controllers/Api/Swagger/ProductSwaggerDocumentation.php
namespace App\Http\Controllers\Api\Swagger;

use OpenApi\Attributes as OA;

trait ProductSwaggerDocumentation
{
    #[OA\Get(
        path: '/api/v1/products',
        summary: 'Listar produtos',
        tags: ['Produtos'],
        // ... documentação
    )]
    public function index() {}
}
```

### 2. Usar no Controller

```php
class ProductController extends Controller
{
    use ProductSwaggerDocumentation;

    // ... implementação dos métodos
}
```

### 3. Gerar Documentação

```bash
docker compose exec app php artisan l5-swagger:generate
```

## Boas Práticas

### ✅ Faça

1. **Descrições claras**: Use descrições que expliquem o propósito do endpoint
2. **Exemplos realistas**: Forneça exemplos de dados que façam sentido
3. **Todos os campos**: Documente todos os campos de entrada e saída
4. **Códigos de status**: Documente todos os possíveis códigos de retorno
5. **Organize por Tags**: Agrupe endpoints relacionados com tags
6. **Mantenha atualizado**: Atualize a documentação ao modificar a API

### ❌ Evite

1. **Documentação desatualizada**: Sempre regenere após mudanças
2. **Descrições genéricas**: "Get data" não é útil
3. **Falta de exemplos**: Sempre forneça exemplos
4. **Ignorar erros**: Documente responses de erro também
5. **Misturar concerns**: Mantenha cada trait focado em um recurso

## Troubleshooting

### Documentação não aparece

```bash
# Limpar cache
docker compose exec app php artisan cache:clear

# Regenerar documentação
docker compose exec app php artisan l5-swagger:generate

# Verificar permissões
docker compose exec app chmod -R 777 storage/
```

### Erro 404 ao acessar /api/documentation

Verifique se a rota está registrada:

```bash
docker compose exec app php artisan route:list | grep documentation
```

### Mudanças não aparecem

Sempre regenere após modificar annotations:

```bash
docker compose exec app php artisan l5-swagger:generate
```

## Recursos Adicionais

### Links Úteis

- [OpenAPI Specification](https://swagger.io/specification/)
- [L5-Swagger GitHub](https://github.com/DarkaOnLine/L5-Swagger)
- [Swagger UI](https://swagger.io/tools/swagger-ui/)
- [OpenAPI Generator](https://openapi-generator.tech/)

### Gerando Código Client

Use a especificação JSON para gerar clients em várias linguagens:

```bash
# Baixar a especificação
curl http://localhost:8000/api/documentation.json > api-spec.json

# Gerar client JavaScript
npx @openapitools/openapi-generator-cli generate \
  -i api-spec.json \
  -g javascript \
  -o ./api-client
```

## Resumo

- **URL da Documentação**: http://localhost:8000/api/documentation
- **Especificação JSON**: http://localhost:8000/api/documentation.json
- **Regenerar**: `php artisan l5-swagger:generate`
- **Traits**: Código limpo e organizado
- **Interativa**: Teste endpoints no navegador
- **Padrão OpenAPI**: Compatível com ferramentas do ecossistema

---

**A documentação Swagger torna sua API mais acessível e fácil de usar!**
