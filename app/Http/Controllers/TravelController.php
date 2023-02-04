<?php

namespace App\Http\Controllers;

use App\Enums\TravelStatus;
use App\Exceptions\ActiveTravelException;
use App\Exceptions\CannotCancelFinishedTravelException;
use App\Exceptions\CannotCancelRunningTravelException;
use App\Http\Requests\TravelStoreRequest;
use App\Http\Resources\TravelResource;
use App\Http\Resources\TravelStoreResource;
use App\Models\Travel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TravelController extends Controller
{

	public function view()
	{
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

		if ($theTravel->status == TravelStatus::RUNNING) {
			throw  new CannotCancelRunningTravelException();
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

	public function passengerOnBoard()
	{
	}

	public function done()
	{
	}

	public function take()
	{
	}
}
