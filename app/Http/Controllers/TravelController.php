<?php

namespace App\Http\Controllers;

use App\Enums\TravelEventType;
use App\Enums\TravelStatus;
use App\Exceptions\ActiveTravelException;
use App\Exceptions\CannotCancelFinishedTravelException;
use App\Exceptions\CannotCancelRunningTravelException;
use App\Exceptions\CarDoesNotArrivedAtOriginException;
use App\Exceptions\InvalidTravelStatusForThisActionException;
use App\Http\Requests\TravelStoreRequest;
use App\Http\Resources\TravelResource;
use App\Http\Resources\TravelStoreResource;
use App\Models\Driver;
use App\Models\Travel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TravelController extends Controller
{

	public function view($travel)
	{
		$user = request()->user();
		$theTravel = Travel::where('id', '=', $travel)->where(function ($query) use ($user) {
			$query->where('passenger_id', '=', $user->id)->orWhere('driver_id', '=', $user->id);
		})->firstOrFail();
		return response()->json(TravelStoreResource::make($theTravel), 200);
	}

	public function store(TravelStoreRequest $request)
	{
		$passanger = $request->user();
		$travel_spots = $request->get('spots', null);
		// check if passanger can request for new travel
		if (!Travel::userHasActiveTravel($passanger) && !is_null($travel_spots)) {
			// more optimized to model normal users then abnormal ones.
			DB::beginTransaction();
			$travel = Travel::query()
				->create([
					'passenger_id' => $passanger->id,
					'status' => TravelStatus::SEARCHING_FOR_DRIVER->value,
				]);
			$travel->spots()->createMany($travel_spots);
			DB::commit();
			// return needed results
			return response()->json(TravelStoreResource::make($travel), 201);
		} else {
			throw new ActiveTravelException();
		}
	}

	public function cancel(Request $request, $travel)
	{
		$user = $request->user();
		$theTravel = Travel::where('id', '=', $travel)->where(function ($query) use ($user) {
			$query->where('passenger_id', '=', $user->id)->orWhere('driver_id', '=', $user->id);
		})->firstOrFail();
		
		if ($theTravel->status == TravelStatus::CANCELLED || $theTravel->status == TravelStatus::DONE) {
			throw  new CannotCancelFinishedTravelException();
		}

		// passanger cannot cancel a running travel but drivers can
		if ( !Driver::isDriver($user) && $theTravel->status === TravelStatus::RUNNING ) {
			throw new CannotCancelRunningTravelException();
		}

		// to pass testCancelOnboardPassenger
		// no need to check if travel is in RUNNING status
		if ($theTravel->passengerIsInCar()) {
			throw new CannotCancelRunningTravelException();
		}

		
		

		// if non of above is the case, cancel the travel
		$theTravel->status = TravelStatus::CANCELLED->value;
		$theTravel->save();
		// return results
		return TravelResource::make($theTravel);
	}

	public function passengerOnBoard(Request $request, $travel)
	{
		$driver = $request->user();
		// check if the user is indeed a driver otherwise abort the request
		if(!Driver::isDriver($driver) ) return abort(403);

		$theTravel = Travel::where([['id', '=', $travel], ['driver_id', '=', $driver->id]])->with(['events'])->firstOrFail();
		// to pass testPassengerOnBoardWhenCarIsNotArrived, 
		// should be checked before checking passanger is checked.
		if (!$theTravel->driverHasArrivedToOrigin()) {
			throw new CarDoesNotArrivedAtOriginException();
		}
		
		$isPassangerInTheCar = $theTravel->passengerIsInCar();
		
		// change the status of the last event as passanger is on board
		if($isPassangerInTheCar) {
			$theTravel->events()->create(['type' => TravelEventType::PASSENGER_ONBOARD]);
		}else {
			throw new InvalidTravelStatusForThisActionException();
		}

		


		return TravelResource::make($theTravel);
	}

	public function done()
	{
	}

	public function take()
	{
	}
}
