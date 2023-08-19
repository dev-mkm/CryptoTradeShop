<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', ['only' => ['index', 'show']]);
    }
    
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required'],
        ]);
 
        if (Auth::attempt($credentials)) {
            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => 200,
                'result' => $user->createToken('API-TOKEN')->plainTextToken
            ], 200);
        }
 
        abort(403, 'Invalid credentials');
    }
}
