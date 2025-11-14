<?php

namespace App\Http\Controllers\Api\Swagger;

use OpenApi\Attributes as OA;

trait UserSwaggerDocumentation
{
    #[OA\Get(
        path: '/api/v1/users',
        summary: 'Listar todos os usuários',
        description: 'Retorna uma lista paginada de usuários',
        tags: ['Usuários'],
        parameters: [
            new OA\Parameter(
                name: 'page',
                in: 'query',
                description: 'Número da página',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 1)
            ),
            new OA\Parameter(
                name: 'per_page',
                in: 'query',
                description: 'Quantidade de itens por página',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 15)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de usuários retornada com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '9d7c1a2b-3e4f-5a6b-7c8d-9e0f1a2b3c4d'),
                                    new OA\Property(property: 'name', type: 'string', example: 'João Silva'),
                                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'joao@example.com'),
                                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2025-11-13 20:00:00'),
                                    new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2025-11-13 20:00:00')
                                ]
                            )
                        ),
                        new OA\Property(
                            property: 'meta',
                            properties: [
                                new OA\Property(property: 'total', type: 'integer', example: 50),
                                new OA\Property(property: 'page', type: 'integer', example: 1),
                                new OA\Property(property: 'per_page', type: 'integer', example: 15),
                                new OA\Property(property: 'total_pages', type: 'integer', example: 4)
                            ],
                            type: 'object'
                        )
                    ]
                )
            )
        ]
    )]
    public function index() {}

    #[OA\Get(
        path: '/api/v1/users/{id}',
        summary: 'Buscar usuário por ID',
        description: 'Retorna os dados de um usuário específico',
        tags: ['Usuários'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'UUID do usuário',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Usuário encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '9d7c1a2b-3e4f-5a6b-7c8d-9e0f1a2b3c4d'),
                                new OA\Property(property: 'name', type: 'string', example: 'João Silva'),
                                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'joao@example.com'),
                                new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2025-11-13 20:00:00'),
                                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2025-11-13 20:00:00')
                            ],
                            type: 'object'
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Usuário não encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Usuário não encontrado')
                    ]
                )
            )
        ]
    )]
    public function show() {}

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
                    new OA\Property(property: 'name', type: 'string', minLength: 3, maxLength: 255, example: 'João Silva'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'joao@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 6, example: 'senha123')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Usuário criado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Usuário criado com sucesso'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '9d7c1a2b-3e4f-5a6b-7c8d-9e0f1a2b3c4d'),
                                new OA\Property(property: 'name', type: 'string', example: 'João Silva'),
                                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'joao@example.com'),
                                new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2025-11-13 20:00:00'),
                                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2025-11-13 20:00:00')
                            ],
                            type: 'object'
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Erro de validação',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Validation failed'),
                        new OA\Property(
                            property: 'errors',
                            properties: [
                                new OA\Property(property: 'email', type: 'array', items: new OA\Items(type: 'string', example: 'Email inválido'))
                            ],
                            type: 'object'
                        )
                    ]
                )
            )
        ]
    )]
    public function store() {}

    #[OA\Put(
        path: '/api/v1/users/{id}',
        summary: 'Atualizar usuário',
        description: 'Atualiza os dados de um usuário existente',
        tags: ['Usuários'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'UUID do usuário',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', minLength: 3, maxLength: 255, example: 'João Silva Santos'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'joao.santos@example.com')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Usuário atualizado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Usuário atualizado com sucesso'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '9d7c1a2b-3e4f-5a6b-7c8d-9e0f1a2b3c4d'),
                                new OA\Property(property: 'name', type: 'string', example: 'João Silva Santos'),
                                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'joao.santos@example.com'),
                                new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2025-11-13 20:00:00'),
                                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2025-11-13 20:30:00')
                            ],
                            type: 'object'
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Usuário não encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Usuário não encontrado')
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Erro de validação',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Validation failed'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object'
                        )
                    ]
                )
            )
        ]
    )]
    public function update() {}

    #[OA\Delete(
        path: '/api/v1/users/{id}',
        summary: 'Deletar usuário',
        description: 'Remove um usuário do sistema',
        tags: ['Usuários'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'UUID do usuário',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Usuário deletado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Usuário deletado com sucesso')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Usuário não encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Usuário não encontrado')
                    ]
                )
            )
        ]
    )]
    public function destroy() {}
}
