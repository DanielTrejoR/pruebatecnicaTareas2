<?php

namespace App\Http\Controllers\Api;

use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->authService->login(
            $request->string('email')->toString(),
            $request->string('password')->toString()
        );

        return response()->json([
            'message' => 'Autenticación exitosa.',
            'token' => $token,
        ]);
    }
}
