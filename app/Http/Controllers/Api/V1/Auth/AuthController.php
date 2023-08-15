<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    /**
     * Registration method
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
        ]);

        $token = $user->createToken('main')->plainTextToken;
        return response()->json([
            'token' => $token,
            'user' => $user
        ], 201); 
    }

    /**
     * Login method
     */
    public function login(LoginRequest $request)
    {
        $email = $request->email;
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'Invalid credentials'
            ], 422);
        }

        $token = $user->createToken('main')->plainTextToken;
        return response()->json([
            'token' => $token,
            'user' => $user
        ], 200); 
    }

    /**
     * Logout method
     */
    public function logout(Request $request)
    {
        $user = auth()->user();
        $user->currentAccessToken()->delete();

        return response()->json([], 204);
    }
}
