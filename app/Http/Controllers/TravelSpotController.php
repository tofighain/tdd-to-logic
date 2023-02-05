<?php

namespace App\Http\Controllers;

use App\Enums\TravelStatus;
use App\Exceptions\InvalidTravelStatusForThisActionException;
use App\Exceptions\SpotAlreadyPassedException;
use App\Http\Resources\TravelResource;
use App\Models\Driver;
use App\Models\Travel;
use Illuminate\Http\Request;

class TravelSpotController extends Controller
{
	public function arrived(Request $request, $travel, $spot)
	{
		$driver = $request->user();
		// check if the user is indeed a driver otherwise abort the request
		if(!Driver::isDriver($driver) ) return abort(403);
		$theTravel = Travel::where('id', '=', $travel)->firstOrFail();

		if($theTravel->status != TravelStatus::RUNNING) 
			throw new InvalidTravelStatusForThisActionException();
		
		if($theTravel->driverHasArrivedToOrigin())
			throw new SpotAlreadyPassedException();
		
		// fill arrived time to the origin arrived_at
		// recreate origin:
		$origin = $theTravel->getOriginSpot();
		$origin->arrived_at = date('Y-m-d H:i:s');
		$origin->save();
		
		return TravelResource::make($theTravel);
	}

	public function store()
	{
	}

	public function destroy()
	{
	}
}
