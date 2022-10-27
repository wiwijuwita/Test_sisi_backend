<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\AuthLoginRequest;
use App\Http\Requests\Auth\AuthRegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(AuthRegisterRequest $request)
    {
        try {
            $result = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);
            return response()->json($result);
        } catch (Exception $ex) {
            return response()->json(
                ['message' => $ex->getMessage()],
                500
            );
        }
    }

    public function login(AuthLoginRequest $request)
    {
        try {
            $data = [
                'grant_type' => 'password',
                'client_id' => $request->client_id,
                'client_secret' => $request->client_secret,
                'username' => $request->username,
                'password' => $request->password
            ];
            $httpResponse = app()->handle(
                Request::create('/oauth/token', 'POST', $data)
            );
            $result = json_decode($httpResponse->getContent());
            if ($httpResponse->getStatusCode() !== 200) {
                throw new Exception($result->message);
            }
            return response()->json($result);
        } catch (Exception $ex) {
            return response()->json(
                ['message' => $ex->getMessage()],
                500
            );
        }
    }

    public function me(Request $request)
    {
        try {
            return response()->json($request->user('api'));
        } catch (Exception $ex) {
            return response()->json(
                ['message' => $ex->getMessage()],
                500
            );
        }
    }

    public function logout(Request $request)
    {
        try {
            return response()->json(
                $request->user('api')
                    ->token()
                    ->revoke()
            );
        } catch (Exception $ex) {
            return response()->json(
                ['message' => $ex->getMessage()],
                500
            );
        }
    }

}
