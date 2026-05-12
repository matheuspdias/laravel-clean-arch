<?php

namespace App\Http\Controllers\Api\Auth;

use App\Application\Auth\DTOs\Request\LoginDTO;
use App\Application\Auth\UseCases\LoginUseCase;
use App\Application\Auth\UseCases\LogoutUseCase;
use App\Application\User\UseCases\GetUserUseCase;
use App\Http\Controllers\Api\Swagger\AuthSwaggerDocumentation;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use AuthSwaggerDocumentation;

    public function login(Request $request, LoginUseCase $useCase): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $output = $useCase->execute(LoginDTO::fromArray($request->all()));

            return response()->json([
                'message' => 'Login realizado com sucesso',
                'data'    => [
                    'token'      => $output->token,
                    'token_type' => $output->tokenType,
                    'user'       => UserResource::make($output->user->toArray())->resolve(),
                ],
            ]);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    public function logout(Request $request, LogoutUseCase $useCase): JsonResponse
    {
        $useCase->execute($request->bearerToken());

        return response()->json(['message' => 'Logout realizado com sucesso']);
    }

    public function me(Request $request, GetUserUseCase $useCase): JsonResponse
    {
        $output = $useCase->execute($request->user()->id);

        return response()->json([
            'data' => UserResource::make($output->toArray())->resolve(),
        ]);
    }
}
