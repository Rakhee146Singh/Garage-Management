<?php

namespace App\Http\Controllers\V1;

use App\Models\State;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StateController extends Controller
{
    /**
     * API of listing State data.
     *
     * @return $states
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
        $query = State::query(); //query

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
        $states   = $query->get();
        $data       = [
            'count' => $count,
            'data'  => $states
        ];
        return ok('State list', $data);
    }

    /**
     * API of new create State.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response $state
     */
    public function create(Request $request)
    {
        $request->validate([
            'country_id'      => 'required|exists:countries,id',
            'name'            => 'required|alpha',
        ]);
        $state = State::create($request->only('country_id', 'name'));
        return ok('State created successfully!', $state->load('countries'));
    }

    /**
     * API to get State with $id.
     *
     * @param  \App\State  $id
     * @return \Illuminate\Http\Response $state
     */
    public function show($id)
    {
        $state = State::with('cities')->findOrFail($id);
        return ok('State retrieved successfully', $state);
    }

    /**
     * API of Update State Data.
     *
     * @param  \App\State  $id
     * @return \Illuminate\Http\Response $state
     */
    public function update(Request $request, $id)
    {
        $state = State::findOrFail($id);
        $request->validate([
            'country_id'      => 'required|exists:countries,id',
            'name'            => 'required|alpha',
        ]);
        $state->update($request->only('country_id', 'name'));
        return ok('State updated successfully!', $state->load('countries'));
    }

    /**
     * API of Delete State data.
     *
     * @param  \App\State  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $state = State::findOrFail($id);
        if ($state->cities()->count() > 0) {
            $state->cities()->delete();
        }
        $state->delete();
        return ok('State deleted successfully');
    }
}
