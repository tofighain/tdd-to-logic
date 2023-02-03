<?php
namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\RegisterResource;
use App\Models\User;

class AuthController extends Controller {
	public function register(RegisterRequest $request) {
		$user = User::create(
			[
				"name" => $request->name,
				"lastname" => $request->lastname,
				"cellphone" => $request->cellphone,
				"password" => $request->password,
			]
		);
		return RegisterResource::make($user)->response()->setStatusCode(200);
	}

	public function user() {
	}
}
