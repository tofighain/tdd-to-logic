<?php

namespace App\Http\Controllers;

use App\Enums\TravelStatus;
use App\Exceptions\InvalidTravelStatusForThisActionException;
use App\Exceptions\SpotAlreadyPassedException;
use App\Http\Requests\TravelSpotStoreRequest;
use App\Http\Resources\TravelResource;
use App\Models\Driver;
use App\Models\Travel;
use App\Models\TravelSpot;
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

	public function store(TravelSpotStoreRequest $request, $travel)
	{
		$passanger = $request->user();
		// check if the user is indeed a passanger otherwise abort the request
		if(Driver::isDriver($passanger) ) return abort(403);
		

		// add the spot to the travel spots
		// dd($request->all());
		// dd($request->$request->only("latitude", "longitude", "position"));
		// dd($theTravel->spots);
		
		// based on noted i documented in todo.md i increment the position 
		// by one instead of trusting user input. 
		$latestPositionOfTravel = TravelSpot::where('travel_id', $travel)->max('position');

		if ($request->position > $latestPositionOfTravel) {
            return response()->json([
                'errors' => [
                    'position' => 'error'
                ]
            ], 422);
        }
		// refresh and recreate the travel
		$theTravel = Travel::where('id', '=', $travel)->firstOrFail();

		if ($theTravel->allSpotsPassed()) {
			throw new SpotAlreadyPassedException();
		}

		if($theTravel->status != TravelStatus::RUNNING)
			throw new InvalidTravelStatusForThisActionException();

		TravelSpot::create([
			"travel_id" => $travel,
			"latitude" => $request->latitude , 
			"longitude" => $request->longitude , 
			"position" => $latestPositionOfTravel +1,
		]);
		return TravelResource::make($theTravel);

	}

	public function destroy(Request $request, $travel, $spot)
	{
		$passanger = $request->user();
		// check if the user is indeed a passanger otherwise abort the request
		if(Driver::isDriver($passanger) ) return abort(403);

		$theTravel = Travel::where('id', '=', $travel)->firstOrFail();
		if ($theTravel->allSpotsPassed())
			throw new SpotAlreadyPassedException();
		

		if($theTravel->status != TravelStatus::RUNNING)
			throw new InvalidTravelStatusForThisActionException();
		
		TravelSpot::where([['travel_id', '=', $travel],['id', '=', $spot]])->delete();
		TravelSpot::where([['travel_id', '=', $travel],['id', '>', $spot]])->decrement('position');
		return TravelResource::make($theTravel);
	}
}
