<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;
use MyHelper;

class ApiController extends Controller
{
    //register
    public function register(Request $request)
    {
        $data = $request->only('name', 'email', 'password');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50'
        ]);

        // check if vaidation not success
        if ($validator->fails()) {
            return MyHelper::responseAPI(false, $validator->errors()->first(), [], Response::HTTP_BAD_REQUEST);
        }

        // Create User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        if ($user) {
            return MyHelper::responseAPI(true, 'User created succesfully',  $user, Response::HTTP_CREATED);
        }
    }

    // login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (!$token = JWTAuth::attempt($credentials)) {
            return MyHelper::responseAPI(false, 'Unauthorized', [], Response::HTTP_UNAUTHORIZED);
        }

        $data = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL(),
        ];
        return MyHelper::responseAPI(true, 'Authorized', $data, Response::HTTP_OK);
    }

    public function logout(Request $request)
    {
        JWTAuth::invalidate();
        return response()->json([
            'success' => false,
            'message' => 'Succesfully logged out'
        ]);
    }

    public function me(Request $request)
    {
        try {
            $user = JWTAuth::authenticate($request->token);
            return MyHelper::responseAPI(true, 'success', $user, Response::HTTP_OK);
        } catch (UserNotDefinedException $e) {
            return MyHelper::responseAPI(false, $e, [], Response::HTTP_UNAUTHORIZED);
        }
    }
}
