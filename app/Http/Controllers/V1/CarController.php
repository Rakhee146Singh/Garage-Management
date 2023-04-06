<?php

namespace App\Http\Controllers\V1;

use App\Models\Car;
use App\Models\User;
use App\Mail\ServiceMail;
use App\Models\CarService;
use App\Models\GarageUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\UpdateCarMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CarController extends Controller
{
    /**
     * API of listing Car data.
     *
     * @return json $cars
     */
    public function list(Request $request)
    {
        $request->validate(
            [
                'search'        => 'nullable|string',
                'sortOrder'     => 'nullable|in:asc,desc',
                'sortField'     => 'nullable|string',
                'perPage'       => 'nullable|integer',
                'currentPage'   => 'nullable|integer',
            ]
        );

        $query = Car::query()->with('carServices.jobs.users'); //query

        /** Listing Car details for customer */
        if (auth()->user()->type == 'customer') {
            $query = $query->with('carServices.jobs.users');
        }

        /** Listing Car details for mechanic */
        if (auth()->user()->type == 'mechanic') {
            $query = $query->with('carServices');
        }

        /* Searching */
        if (isset($request->search)) {
            $query = $query->where("company_name", "LIKE", "%{$request->search}%");
        }
        /* Sorting */
        if ($request->sortField && $request->sortOrder) {
            $query = $query->orderBy($request->sortField, $request->sortOrder);
        }

        /* Pagination */
        $count = $query->count();
        if ($request->perPage && $request->currentPage) {
            $perPage        = $request->perPage;
            $currentPage    = $request->currentPage;
            $query          = $query->skip($perPage * ($currentPage - 1))->take($perPage);
        }

        /* Get records */
        return ok('Car list', [
            'count' => $count,
            'cars'  => $query->get()
        ]);
    }

    /**
     * API of new create Car.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json $car
     */
    public function create(Request $request)
    {
        $request->validate(
            [
                'garage_id'                     => 'required|exists:garages,id',
                'company_name'                  => 'required|alpha|max:20',
                'model_name'                    => 'required|string|max:20',
                'manufacturing_year'            => 'required|date_format:Y',
                'service_type_id.*'             => 'required|exists:service_types,id',
            ]
        );

        $car = Car::create(
            $request->only(
                'garage_id',
                'company_name',
                'model_name',
                'manufacturing_year'
            ) +
                [
                    'user_id' => Auth::id()
                ]
        );
        $user = $car->users;

        /** Insertion in Car Service Table with Car Details */
        $services = [];
        foreach ($request->service_type_id as $service_id) {
            $service = CarService::create(
                [
                    'garage_id'         => $request->garage_id,
                    'car_id'            => $car->id
                ] +
                    [
                        'service_type_id'   => $service_id
                    ]
            );
            array_push($services, $service);
        }

        /** If car created by mechanic or customer send mail to Garage owner with Car Service Id */
        if (auth()->user()->type == 'mechanic' || auth()->user()->type == 'customer') {
            $owner_data = GarageUser::where('garage_id', $request->garage_id)->where('is_owner', true)->first();
            $owner      = User::findOrFail($owner_data->user_id);
            Mail::to($owner->email)->send(new ServiceMail($owner, $user, $car, $services));
        }
        return ok('Car created successfully!', $car->load('carServices'));
    }

    /**
     * API to get Car with $id.
     *
     * @param  \App\Car  $id
     * @return json $car
     */
    public function show($id)
    {
        $car = Car::with('carServices')->findOrFail($id);
        return ok('Car retrieved successfully', $car);
    }

    /**
     * API of Update Car Data.
     *
     * @param  \App\Car  $id
     * @return json $car
     */
    public function update(Request $request, $id)
    {
        $car = $request->validate(
            [
                'garage_id'             => 'required|exists:garages,id',
                'company_name'          => 'required|alpha|max:20',
                'model_name'            => 'required|string|max:30',
                'manufacturing_year'    => 'required|date_format:Y',
                'service_type_id.*'     => 'required|exists:service_types,id',
            ]
        );

        $car = Car::findOrFail($id);
        $car->update(
            $request->only(
                'company_name',
                'model_name',
                'manufacturing_year'
            ) +
                [
                    'user_id' => Auth::id()
                ]
        );

        /** Insertion in Car Service Table with Car Details */
        $services = [];
        foreach ($request->service_type_id as $service_id) {
            $service = $car->carServices()->updateOrCreate(
                [
                    'garage_id'         => $request->garage_id,
                    'car_id'            => $car->id
                ] +
                    [
                        'service_type_id'   => $service_id
                    ]
            );
            array_push($services, $service);
        }

        /** If car updated by mechanic or customer send mail to Garage owner with Updated Car Service Id */
        if (auth()->user()->type == 'mechanic' || auth()->user()->type == 'customer') {
            $user       = $car->users;
            $owner_data = GarageUser::where('garage_id', $request->garage_id)->where('is_owner', true)->first();
            $owner      = User::findOrFail($owner_data->user_id);
            Mail::to($owner->email)->send(new UpdateCarMail($owner, $user, $car, $services));
        }
        return ok('Car Updated successfully!', $car->load('carServices'));
    }

    /**
     * API of Delete Car data.
     *
     * @param  \App\Car  $id
     * @return json
     */
    public function delete($id)
    {
        $car = Car::findOrFail($id);
        $car->carServices()->first()->jobs()->delete();
        $car->carServices()->delete();
        $car->delete();
        return ok('Car deleted successfully');
    }
}
