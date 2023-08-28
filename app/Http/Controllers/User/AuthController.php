<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:sanctum', ['only' => ['authenticate', 'store']]);
        $this->middleware('auth:sanctum', ['only' => ['update', 'index', 'destroy', 'crypto']]);
    }

    public function index(Request $request)
    {
        abort_unless($request->user()->is_admin(), 403, 'Access denied');
        $cryptos = DB::select('SELECT `id` ,`name`, `email`, `balance`, `admin` from `users`');
        return response()->json([
            'status' => 200,
            'result' => $cryptos
        ]);
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

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $cryptos = DB::select("SELECT `id` from `users` where email = ?", [
            $validated['email'],
        ]);
        abort_if(count($cryptos) > 0, 403, 'User already exists');
        $insert = User::create($validated);
        abort_unless($insert, 500, 'Server Error');
        return response()->json([
            'status' => 200
        ]);
    }

    public function update(UpdateUserRequest $request)
    {
        $validated = $request->validated();
        $updates = [];
        foreach ($validated as $key => $value) {
            $updates[] = "`$key` = '$value'";
        }
        $insert = DB::update("UPDATE `users` set ".implode(',', $updates)." where `id` = ?", [
            $request->user()->id,
        ]);
        abort_unless($insert > 0, 400, 'No Changes Made');
        return response()->json([
            'status' => 200,
            'result' => $insert
        ]);
    }

    public function role(UpdateRoleRequest $request, string $user)
    {
        $validated = $request->validated();
        $updates = [];
        foreach ($validated as $key => $value) {
            $updates[] = "`$key` = '$value'";
        }
        $insert = DB::update("UPDATE `users` set ".implode(',', $updates)." where `id` = ?", [
            $user,
        ]);
        abort_unless($insert > 0, 400, 'No Changes Made');
        return response()->json([
            'status' => 200,
            'result' => $insert
        ]);
    }

    public function destroy(Request $request,string $user)
    {
        abort_unless($request->user()->is_admin(), 403, 'Access denied');
        $insert = DB::delete("DELETE from `users` where `id` = ?", [
            $user,
        ]);
        abort_unless($insert > 0, 404, 'User not found');
        return response()->json([
            'status' => 200,
            'result' => $insert
        ]);
    }

    public function crypto(Request $request, string $user)
    {
        abort_unless($request->user()->is_admin() || $request->user()->id == $user || $user == 'me', 403, 'Access denied');
        if($user == 'me') {
            $user = $request->user()->id;
        }
        $cryptos = DB::select('SELECT cryptos.slug,cryptos.name,cryptos.logo,cryptoBalance.balance from cryptos
        left join (
            SELECT crypto_id, MAX(`c_t_id`) as MaxTime
            FROM cryptoBalance
            WHERE user_id = ?
            GROUP BY crypto_id
        ) r
        on r.crypto_id = cryptos.id
        left join cryptoBalance
        ON cryptoBalance.crypto_id = cryptos.id AND r.MaxTime = cryptoBalance.c_t_id', [
            $user
        ]);
        return response()->json([
            'status' => 200,
            'result' => $cryptos
        ]);
    }
}