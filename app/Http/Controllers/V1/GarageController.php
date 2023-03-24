<?php

namespace App\Http\Controllers\V1;

use App\Models\Garage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GarageController extends Controller
{
    /**
     * API of listing Garage data.
     *
     * @return json $garages
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
        $query = Garage::query(); //query

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
        $garages  = $query->get();
        $data       = [
            'count'     => $count,
            'garages'   => $garages
        ];
        return ok('Garage list', $data);
    }

    /**
     * API of new create Garage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json $garage
     */
    public function create(Request $request)
    {
        $request->validate([
            'city_id'                       => 'required|exists:cities,id',
            'state_id'                      => 'required|exists:states,id',
            'country_id'                    => 'required|exists:countries,id',
            'name'                          => 'required|string|max:30',
            'address1'                      => 'required|string|max:50',
            'address2'                      => 'required|string|max:50',
            'zipcode'                       => 'required|integer|min:6',
            'user_id'                       => 'required|exists:users,id',
            'services.*'                    => 'required|array',
            'services.*.service_type_id'    => 'required|integer'
        ]);
        $garage = Garage::create($request->only('city_id', 'state_id', 'country_id', 'name', 'address1', 'address2', 'zipcode', 'user_id'));

        //enter data in pivot table
        $garage->users()->attach([$request->user_id => ['is_owner' => true]]);
        $garage->services()->attach($request->services);
        return ok('Garage created successfully!', $garage->load('users', 'services'));
    }

    /**
     * API to get Garage with $id.
     *
     * @param  \App\Country  $id
     * @return json $garage
     */
    public function show($id)
    {
        $country = Garage::with('users', 'services')->findOrFail($id);
        return ok('Country retrieved successfully', $country);
    }

    /**
     * API of Update Garage Data.
     *
     * @param  \App\Garage  $id
     * @return json $garage
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'city_id'                       => 'required|exists:cities,id',
            'state_id'                      => 'required|exists:states,id',
            'country_id'                    => 'required|exists:countries,id',
            'name'                          => 'required|string|max:30',
            'address1'                      => 'required|string|max:50',
            'address2'                      => 'required|string|max:50',
            'zipcode'                       => 'required|integer|min:6',
            'user_id'                       => 'required|exists:users,id',
            'services.*'                    => 'required|array',
            'services.*.service_type_id'    => 'required|integer'
        ]);
        $garage = Garage::findOrFail($id);
        $garage->update($request->only('city_id', 'state_id', 'country_id', 'name', 'address1', 'address2', 'zipcode', 'user_id'));

        //enter data in pivot table
        $garage->users()->sync([$request->user_id => ['is_owner' => true]]);
        $garage->services()->sync($request->services);
        return ok('Garage Updated successfully!', $garage->load('users', 'services'));
    }

    /**
     * API of Delete Garage data.
     *
     * @param  \App\Garage  $id
     * @return json
     */
    public function delete($id)
    {
        $garage = Garage::findOrFail($id);
        $garage->users()->delete();
        return ok('Garage deleted successfully');
    }
}
