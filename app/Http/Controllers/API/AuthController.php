<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Generate a Sanctum token for the user
            $token = $user->createToken('API Token')->plainTextToken;
            $cookies = $request->cookie();


            return response()->json([
                'user' => $user,
                'token' => $token,
                'cookies' => $cookies
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function register(Request $request)
    {

        // Validate the request data
        $validatedData = $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:4|confirmed',
        ]);

        // Create a new user
        $user = User::create([
            'name' => 'Admin',
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        // Return a JSON response with the created user
        return response()->json(['user' => $user], 201);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json(['message' => 'Logged out'], 200);
    }

    public function session(Request $request)
    {
        return response()->json((object)['user' => Auth::user()], 200);
        if (Auth::check()) {
            return response()->json(['user' => Auth::user()], 200);
        }

        return response()->json( (object)[], 200);
    }

    public function providers(Request $request)
    {

        $providers = [
            'credentials' => [
                "id" => "credentials",
                'name' => 'Credentials',
                "type" => "credentials",
                "signinUrl" => "http://localhost:8000/api/auth/signin/credentials",
                "callbackUrl"  => "http://localhost:8000/api/auth/callback/credentials"
                ],
            'google' => [
                'id' => 'google',
                'name' => 'Google',
                'type' => 'oauth',
                'signinUrl' => route('google.login', ['provider' => 'google']),
                'callbackUrl' => route('google.callback', ['provider' => 'google']),
            ],
            // Add more providers as needed
        ];

        return response()->json((object)$providers, 200);

    }


    public function signin(Request $request)
    {
        // Implement social login logic
        return response()->json([], 200);
    }

    public function signinProvided(Request $request, $provider)
    {
        // Implement social login logic
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function callbackProvider(Request $request, $provider)
    {
        $params = $request->all();

        $callbackUrl = $request->get('callbackUrl');
        $csrfToken = $request->get('csrfToken');

        if ($provider === 'credentials'){
            $email = $params['email'];
            $password = $params['password'];
            $credentials = ['email' => $email, 'password' => $password];
            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                $cookieCsrf = cookie('next-auth.csrf-token', $csrfToken, 0, '/', null, false, true, false, 'lax', 0);
                $cookieUrl  = cookie('next-auth.callback-url', $callbackUrl, 0, '/', null, false, true, false, 'lax', 0);


                // Validate CSRF token
                if ($csrfToken) {
                    return response()->json((object)['url' => $callbackUrl], 200)
                        ->withCookie($cookieCsrf)
                        ->withCookie($cookieUrl);
                } else {
                    return response()->json(['error' => 'Invalid CSRF token'], 403);
                }
            }
            return response()->json(['error' => 'Unauthorized'], 401);
        }

    }


    public function csrf(Request $request)
    {
        $token = csrf_token();

        if (!$token) {
            return response()->json(['error' => 'CSRF token generation failed'], 500);
        }

        return response()->json(['csrfToken' => $token]);

    }
}
