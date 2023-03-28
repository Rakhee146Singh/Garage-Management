<?php

namespace App\Http\Controllers\V1;

use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CityController extends Controller
{
    /**
     * API of listing City data.
     *
     * @return json $cities
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
        $query = City::query(); //query

        /* Searching */
        if (isset($request->search)) {
            $query = $query->where("name", "LIKE", "%{$request->search}%");
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
        $cities   = $query->get();
        $data       = [
            'count'     => $count,
            'cities'    => $cities
        ];
        return ok('City list', $data);
    }

    /**
     * API of new create City.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json $city
     */
    public function create(Request $request)
    {
        $request->validate([
            'state_id'      => 'required|exists:states,id',
            'name'          => 'required|alpha|max:30',
        ]);
        $city = City::create($request->only('state_id', 'name'));
        return ok('City created successfully!', $city->load('states'));
    }

    /**
     * API to get City with $id.
     *
     * @param  \App\City  $id
     * @return json $city
     */
    public function show($id)
    {
        $city = City::with('states')->findOrFail($id);
        return ok('City retrieved successfully', $city);
    }

    /**
     * API of Update City Data.
     *
     * @param  \App\City  $id
     * @return json $city
     */
    public function update(Request $request, $id)
    {
        $city = City::findOrFail($id);
        $request->validate([
            'state_id'      => 'required|exists:states,id',
            'name'          => 'required|alpha|max:30',
        ]);
        $city->update($request->only('state_id', 'name'));
        return ok('City updated successfully!', $city->load('states'));
    }

    /**
     * API of Delete City data.
     *
     * @param  \App\City  $id
     * @return json
     */
    public function delete($id)
    {
        City::findOrFail($id)->delete();
        return ok('City deleted successfully');
    }
}
