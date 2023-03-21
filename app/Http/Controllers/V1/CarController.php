<?php

namespace App\Http\Controllers\V1;

use App\Models\Car;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CarController extends Controller
{
    /**
     * API of listing Car data.
     *
     * @return $cars
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
        $query = Car::query(); //query

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
            'data'  => $cars
        ];
        return ok('Car list', $data);
    }

    /**
     * API of new create Car.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response $car
     */
    public function create(Request $request)
    {
        $request->validate([
            'user_id'               => 'required|exists:users,id',
            'company_name'          => 'required|alpha|max:20',
            'model_name'            => 'required|string|max:30',
            'manufacturing_year'    => 'required|date_format:Y'
        ]);
        $car = Car::create($request->only('user_id', 'company_name', 'model_name', 'manufacturing_year'));
        return ok('Car created successfully!', $car->load('carServices'));
    }

    /**
     * API to get Car with $id.
     *
     * @param  \App\Car  $id
     * @return \Illuminate\Http\Response $car
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
     * @return \Illuminate\Http\Response $car
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id'               => 'required|exists:users,id',
            'company_name'          => 'required|alpha|max:20',
            'model_name'            => 'required|string|max:30',
            'manufacturing_year'    => 'required|date_format:Y'
        ]);

        $car = Car::findOrFail($id);
        $car->update($request->only('user_id', 'company_name', 'model_name', 'manufacturing_year'));
        return ok('Car Updated successfully!', $car->load('carServices'));
    }

    /**
     * API of Delete Car data.
     *
     * @param  \App\Car  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $car = Car::findOrFail($id);
        $car->carServices()->delete();
        return ok('Car deleted successfully');
    }
}
