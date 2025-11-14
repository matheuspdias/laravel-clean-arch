<?php

namespace App\Http\Controllers\Api\Swagger;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Laravel Clean Architecture API',
    description: 'API de gerenciamento de usuários implementada com Clean Architecture e DDD',
    contact: new OA\Contact(
        name: 'API Support',
        url: 'https://github.com/matheuspdias/laravel-clean-arch'
    )
)]
#[OA\Server(
    url: 'http://localhost:8000',
    description: 'Servidor Local'
)]
#[OA\Server(
    url: 'https://api.production.com',
    description: 'Servidor de Produção'
)]
trait SwaggerInfo
{
    //
}
