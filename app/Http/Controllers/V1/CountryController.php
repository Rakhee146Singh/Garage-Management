<?php

namespace App\Http\Controllers\V1;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CountryController extends Controller
{
    /**
     * API of listing Country data.
     *
     * @return $countries
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
        $query = Country::query(); //query

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
        $countries   = $query->get();
        $data       = [
            'count' => $count,
            'data'  => $countries
        ];
        return ok('Country list', $data);
    }

    /**
     * API of new create Country.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response $country
     */
    public function create(Request $request)
    {
        $request->validate([
            'name'         => 'required',
        ]);
        $country = Country::create($request->only('name'));
        return ok('Country created successfully!', $country);
    }

    /**
     * API to get Country with $id.
     *
     * @param  \App\Country  $id
     * @return \Illuminate\Http\Response $country
     */
    public function show($id)
    {
        $country = Country::findOrFail($id);
        return ok('Country retrieved successfully', $country);
    }

    /**
     * API of Update Country Data.
     *
     * @param  \App\Country  $id
     * @return \Illuminate\Http\Response $country
     */
    public function update(Request $request, $id)
    {
        $country = Country::findOrFail($id);
        $request->validate([
            'name'         => 'required',
        ]);
        $country->update($request->only('name'));
        return ok('Country updated successfully!', $country);
    }

    /**
     * API of Delete Country data.
     *
     * @param  \App\Country  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        Country::findOrFail($id)->delete();
        return ok('Country deleted successfully');
    }
}
