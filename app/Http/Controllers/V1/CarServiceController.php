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
     * @return $carservices
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
        $carservices   = $query->get();
        $data       = [
            'count' => $count,
            'data'  => $carservices
        ];
        return ok('Car Service list', $data);
    }

    /**
     * API of new create Car Service.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response $carservice
     */
    public function create(Request $request)
    {
        $request->validate([
            'garage_id'       => 'required',
            'car_id'          => 'required',
            'status'          => 'required|in:Initiated,In-Progress,Delay,Completed, Delivered',
        ]);
        $carservice = CarService::create($request->only('garage_id', 'car_id', 'status'));
        return ok('Car Service created successfully!', $carservice->load('jobs'));
    }

    /**
     * API to get Car Service with $id.
     *
     * @param  \App\CarService  $id
     * @return \Illuminate\Http\Response $carservice
     */
    public function show($id)
    {
        $carservice = CarService::with('jobs')->findOrFail($id);
        return ok('Car Service retrieved successfully', $carservice);
    }

    /**
     * API of Update Car Service Data.
     *
     * @param  \App\CarService  $id
     * @return \Illuminate\Http\Response $carservice
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'garage_id'       => 'required',
            'car_id'          => 'required',
            'status'          => 'required|in:Initiated,In-Progress,Delay,Completed, Delivered',
        ]);

        $carservice = CarService::findOrFail($id);
        $carservice->update($request->only('garage_id', 'car_id', 'status'));
        return ok('Car Service Updated successfully!', $carservice->load('jobs'));
    }

    /**
     * API of Delete Car Service data.
     *
     * @param  \App\CarService  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        CarService::findOrFail($id)->delete();
        return ok('Car Service deleted successfully');
    }
}
