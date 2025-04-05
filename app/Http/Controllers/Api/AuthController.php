<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginApiRequest;
use App\Http\Requests\RefreshTokenRequest;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    /**
     * Login user
     */
    public function login(LoginApiRequest $request): JsonResponse
    {
        if (Auth::attempt(['login' => $request->login, 'password' => $request->password])) {
            $user = Auth::user();

            $response = Http::post(env('OAUTH_URL') . '/oauth/token', [
                'grant_type' => 'password',
                'client_id' => env('PASSPORT_PASSWORD_CLIENT_ID'),
                'client_secret' => env('PASSPORT_PASSWORD_SECRET'),
                'username' => $request->login,
                'password' => $request->password,
                'scope' => '',
            ]);

            $user['token'] = $response->json();

            return response()->json([
                'success' => true,
                'statusCode' => 200,
                'message' => 'User has been logged successfully.',
                'data' => $user,
            ], 200);
        } else {
            return response()->json([
                'success' => true,
                'statusCode' => 401,
                'message' => 'Unauthorized.',
                'errors' => 'Unauthorized',
            ], 401);
        }

    }

    /**
     * refresh token
     *
     * @param RefreshTokenRequest $request
     * @return JsonResponse
     * @throws ConnectionException
     */
    public function refreshToken(RefreshTokenRequest $request): JsonResponse
    {
        $response = Http::asForm()->post(env('APP_URL') . '/oauth/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->refresh_token,
            'client_id' => env('PASSPORT_PASSWORD_CLIENT_ID'),
            'client_secret' => env('PASSPORT_PASSWORD_SECRET'),
            'scope' => '',
        ]);

        return response()->json([
            'success' => $response->status() == 200,
            'message' => $response->status() == 200 ? 'Refreshed token.' : 'error',
            'data' => $response->json(),
        ], $response->status());
    }

}
