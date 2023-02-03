<?php

namespace App\Http\Controllers;

use App\Enums\DriverStatus;
use App\Http\Requests\DriverSignupRequest;
use App\Http\Resources\DriverResource;
use App\Models\Driver;

class DriverController extends Controller
{
	public function signup(DriverSignupRequest $request)
	{
		// 1) Authenticate user (check if user is signed in)
		if ($request->user('sanctum')) {
			$user = $request->user('sanctum');
		} else {
			return  response()->json([
				'code'=>"NotAUser"
			] ,401);
		}

		// 2) If user is a driver already he/she can't request again
		// because it is more likely that a non driver requests for being a
		// driver so !Diver should be programmed first and only if it is not 
		// the case programm goes furthur. 
		if(!Driver::isDriver($user)){
			// i usually use static calls instead of new objects 
			// because they are faster
			$driver = Driver::create(
				[
					"id" => $user->id,
					"car_plate" => $request->car_plate,
					"car_model" => $request->car_model,
					// initially driver is not working :D
					"status" => DriverStatus::NOT_WORKING->value, 
				]
			);
			return response()->json(DriverResource::make($driver));
		}
		// 3) If user is a driver return "code" => "AlreadyDriver" with status code 400
		return  response()->json(['code'=>"AlreadyDriver"] ,400);
		// no need for else block here !
	}

	public function update()
	{
	}
}
