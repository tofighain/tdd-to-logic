<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\RegisterResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
	public function register(RegisterRequest $request)
	{
		$user = User::create(
			[
				"name" => $request->name,
				"lastname" => $request->lastname,
				"cellphone" => $request->cellphone,
				"password" => $request->password,
			]
		);
		$user['token'] = $user->createToken('api-token')->plainTextToken;

		return RegisterResource::make($user)->response()->setStatusCode(200);
	}
	
	public function user(UserRequest $request)
	{
		// note that an especialized request called UserRequest is 
		// generated for furture imporvments 
		// may for service providers
		$user = User::where('id', $request->user()->id)->firstOrFail();
		return UserResource::make($request->user())->response()->setStatusCode(200);
	}
}
