<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{   

    /**
     * Summary of login
     * validate email and password for login
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse jwt token if sucess else status 401
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['invalid credentials'], 401);
            }
            return response()->json([
                'token' => $token,
                'user' => auth()->user()
            ]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }
    }
    
    /**
     * Summary of logout
     * log out user by invalidating current token
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(["message" => "Logged out successfully"]);
    }
    
    /**
     * Summary of refresh
     * refresh token before access token expiries
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            $newtoken = JWTAuth::parseToken()->refresh();
            return response()->json([
                "token" => $newtoken,
                "user" => auth()->user()
            ]);
        } catch (JWTException $e) {
            return response()->json(["error" => "failed to refresh token"], 401);
        }
    }
}
