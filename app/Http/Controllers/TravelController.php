<?php

namespace App\Http\Controllers;

use App\Enums\TravelStatus;
use App\Exceptions\ActiveTravelException;
use App\Http\Requests\TravelStoreRequest;
use App\Http\Resources\TravelResource;
use App\Http\Resources\TravelStoreResource;
use App\Models\Travel;
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
			return response()->json(TravelStoreResource::make($travel) , 201);
		} else {
			throw new ActiveTravelException();
		}
	}

	public function cancel()
	{
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
