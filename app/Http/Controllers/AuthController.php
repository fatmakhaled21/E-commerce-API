<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'signup']]);
    }

    public function login(Request $request): JsonResponse
    {
        // Validate the request inputs
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email', // Ensure the email exists
            'password' => 'required|string|min:6'
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Check if the password matches
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Create a token for the authenticated user
        $token = auth('api')->login($user);

        // Return the new token
        return $this->createNewToken($token);
    }

    public function signup(Request $request): JsonResponse
    {
        // Validate the signup input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email', // Ensure email is unique
            'password' => 'required|string|min:6',
        ]);

        // Create a new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Log in the user and create a token
        $token = auth('api')->login($user);

        // Return the success response with the token
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function logout(): JsonResponse
    {
        // Log out the user (invalidate the token)
        auth('api')->logout();
        return response()->json(['message' => 'User is logged out']);
    }

    public function refresh(): JsonResponse
{
    try {
        // Attempt to refresh the token
        $newToken = auth('api')->refresh;
        
        return response()->json([
            'status' => 'success',
            'user' => auth('api')->user(),
            'authorisation' => [
                'token' => $newToken,
                'type' => 'bearer',
            ]
        ]);
    } catch (\Exception $e) {
        // Handle errors, such as expired or invalid token
        return response()->json([
            'error' => 'Token refresh failed. Please login again.',
            'message' => $e->getMessage()
        ], 401);
    }
}


protected function createNewToken($token): JsonResponse
{
    return response()->json([
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => config('jwt.ttl') * 60,  // Fetch the TTL from the JWT config
        'user' => auth('api')->user(),
    ]);
}


}

// factory()->getTTL() * 60, 
