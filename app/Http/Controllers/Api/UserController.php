<?php

namespace App\Http\Controllers\Api;

use App\Application\User\DTOs\Request\CreateUserDTO;
use App\Application\User\DTOs\Request\ListUsersDTO;
use App\Application\User\DTOs\Request\UpdateUserDTO;
use App\Application\User\UseCases\CreateUserUseCase;
use App\Application\User\UseCases\DeleteUserUseCase;
use App\Application\User\UseCases\GetUserUseCase;
use App\Application\User\UseCases\ListUsersUseCase;
use App\Application\User\UseCases\UpdateUserUseCase;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request, ListUsersUseCase $useCase): JsonResponse
    {
        $dto = ListUsersDTO::fromArray($request->query());
        $output = $useCase->execute($dto);

        return response()->json([
            'data' => array_map(fn($user) => UserResource::make($user->toArray())->resolve(), $output->users),
            'meta' => [
                'total' => $output->total,
                'page' => $output->page,
                'per_page' => $output->perPage,
                'total_pages' => $output->totalPages(),
            ]
        ]);
    }

    public function store(Request $request, CreateUserUseCase $useCase): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $dto = CreateUserDTO::fromArray($request->all());
            $output = $useCase->execute($dto);

            return response()->json([
                'message' => 'Usuário criado com sucesso',
                'data' => UserResource::make($output->toArray())->resolve()
            ], 201);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id, GetUserUseCase $useCase): JsonResponse
    {
        try {
            $output = $useCase->execute($id);

            return response()->json([
                'data' => UserResource::make($output->toArray())->resolve()
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id, UpdateUserUseCase $useCase): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $dto = UpdateUserDTO::fromArray($id, $request->all());
            $output = $useCase->execute($dto);

            return response()->json([
                'message' => 'Usuário atualizado com sucesso',
                'data' => UserResource::make($output->toArray())->resolve()
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id, DeleteUserUseCase $useCase): JsonResponse
    {
        try {
            $useCase->execute($id);

            return response()->json([
                'message' => 'Usuário deletado com sucesso'
            ], 200);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao deletar usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
