<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CarService;

class CarServiceController extends Controller
{
    /**
     * API of listing Car Service data.
     *
     * @return json $carservices
     */
    public function list(Request $request)
    {
        $request->validate([
            'search'        => 'nullable|string',
            'sortOrder'     => 'nullable|in:asc,desc',
            'sortField'     => 'nullable|string',
            'perPage'       => 'nullable|integer',
            'currentPage'   => 'nullable|integer'
        ]);
        $query = CarService::query(); //query

        /* Searching */
        if (isset($request->search)) {
            $query = $query->where("car_id", "LIKE", "%{$request->search}%");
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
        $carServices   = $query->get();
        $data       = [
            'count'         => $count,
            'Car Services'  => $carServices
        ];
        return ok('Car Service list', $data);
    }

    /**
     * API of new create Car Service.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json $carService
     */
    public function create(Request $request)
    {
        $request->validate([
            'garage_id'       => 'required|exists:garages,id',
            'car_id'          => 'required|exists:cars,id',
        ]);
        $carService = CarService::create($request->only('garage_id', 'car_id'));
        return ok('Car Service created successfully!', $carService->load('jobs'));
    }

    /**
     * API to get Car Service with $id.
     *
     * @param  \App\CarService  $id
     * @return json $carService
     */
    public function show($id)
    {
        $carService = CarService::with('jobs')->findOrFail($id);
        return ok('Car Service retrieved successfully', $carService);
    }

    /**
     * API of Update Car Service Data.
     *
     * @param  \App\CarService  $id
     * @return json $carService
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'garage_id'       => 'required|exists:garages,id',
            'car_id'          => 'required|exists:cars,id',
        ]);

        $carService = CarService::findOrFail($id);
        $carService->update($request->only('garage_id', 'car_id'));

        return ok('Car Service Updated successfully!', $carService->load('jobs'));
    }

    /**
     * API of Delete Car Service data.
     *
     * @param  \App\CarService  $id
     * @return json
     */
    public function delete($id)
    {
        $carService = CarService::findOrFail($id);
        $carService->jobs()->delete();
        return ok('Car Service deleted successfully');
    }

    /**
     * API for Status of Car Service data.
     *
     * @param  \App\CarService  $id
     * @return json $carService
     */
    public function status(Request $request, $id)
    {
        $request->validate([
            'status'          => 'required|in:Delay,Delivered',
        ]);
        $carService = CarService::findOrFail($id);
        $carService->update($request->only('status'));
        return ok('Car Service status updated successfully', $carService->load('jobs'));
    }
}
