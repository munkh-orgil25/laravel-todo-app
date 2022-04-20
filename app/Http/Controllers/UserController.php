<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
 
class UserController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'getUsers']]);
    }
    
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        
        $token = auth()->attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = auth()->user();
        return response()->json([
                'user' => $user,
                'token' => $token,
            ]);

    }

    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = auth()->login($user);
        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout() {   
        Auth::logout();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function me()
    {
        return response()->json([
            'user' => Auth::user(),
        ]);
    }

    public function getUsers() {
        // return UserResource::collection(User::with(["posts" => function ($query) {
        //     $query->where("finishDue", ">=", "2022-04-13");
        // }])->get());
        return UserResource::collection(User::with("posts")->get());
        // return UserResource::collection(User::all()); <- This is bad!
    }
}
