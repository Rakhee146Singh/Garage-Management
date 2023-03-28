<?php

namespace App\Http\Controllers\V1;

use App\Models\Car;
use App\Models\User;
use App\Mail\ServiceMail;
use App\Models\CarService;
use App\Models\GarageUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Notifications\UpdateCarNotification;

class CarController extends Controller
{
    /**
     * API of listing Car data.
     *
     * @return json $cars
     */
    public function list(Request $request)
    {
        $request->validate([
            'search'        => 'nullable|string',
            'sortOrder'     => 'nullable|in:asc,desc',
            'sortField'     => 'nullable|string',
            'perPage'       => 'nullable|integer',
            'currentPage'   => 'nullable|integer',
            'user_id'       => 'nullable|exists:users,id'
        ]);
        $query = Car::query()->with('carServices.jobs.users')->orderBy('jobs.status', 'desc'); //query

        if ($request->user_id) {
            $query = $query->where('user_id', $request->user_id);
        }
        /* Searching */
        if (isset($request->search)) {
            $query = $query->where("company_name", "LIKE", "%{$request->search}%");
        }
        /* Sorting */
        if ($request->sortField || $request->sortOrder) {
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
        $cars   = $query->get();
        $data       = [
            'count' => $count,
            'cars'  => $cars
        ];
        return ok('Car list', $data);
    }

    /**
     * API of new create Car.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json $car
     */
    public function create(Request $request)
    {
        $request->validate([
            'garage_id'                     => 'required_if:type,customer|exists:garages,id',
            'user_id'                       => 'exists:users,id',
            'cars.*'                        => 'required|array',
            'cars.*.company_name'           => 'required_if:type,customer|alpha|max:20',
            'cars.*.model_name'             => 'required_if:type,customer|string|max:20',
            'cars.*.manufacturing_year'     => 'required_if:type,customer|date_format:Y',
            'cars.*.service_type_id.*'      => 'required_if:type,customer',
        ]);
        $cars = [];
        foreach ($request->cars as $data) {
            $car = Car::create($data + ['user_id' => Auth::id()]);
            foreach ($data['service_type_id'] as $service) {
                $car->types()->attach(['car_id' => $car->id], ['service_type_id' => $service]);
            }
            CarService::create(['garage_id' => $request->garage_id, 'car_id' => $car->id]);
            array_push($cars, $car);
        }
        $user = $car->users;
        $owner_data = GarageUser::where('garage_id', $request->garage_id)->where('is_owner', true)->first();
        $owner = User::findOrFail($owner_data->user_id);
        Mail::to($owner->email)->send(new ServiceMail($owner, $user, $cars));

        /* WRONG FLOW */

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
        $car = $request->validate([
            'user_id'               => 'required|exists:users,id',
            'company_name'          => 'required|alpha|max:20',
            'model_name'            => 'required|string|max:30',
            'manufacturing_year'    => 'required|date_format:Y',
            'service_type_id.*'     => 'required|exists:service_types,id',
        ]);

        $car = Car::findOrFail($id);
        $car->update($request->only('user_id', 'company_name', 'model_name', 'manufacturing_year'));
        $car->types()->sync($request->service_type_id);

        $service = $car->carServices()->first();

        //Send Notification To owner for car update
        $user = User::findOrFail($request->user_id);
        $owner = $user->garages()->first()->users()->first();
        $car->users->notify(new UpdateCarNotification($car, $service, $owner));
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
        $car->carServices()->delete();
        return ok('Car deleted successfully');
    }
}
