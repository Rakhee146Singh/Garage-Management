<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CarServiceJob;

class CarServiceJobController extends Controller
{
    /**
     * API of listing Car Service Job data.
     *
     * @return json $jobs
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
        $query = CarServiceJob::query(); //query

        /* Searching */
        if (isset($request->search)) {
            $query = $query->where("service_type_id", "LIKE", "%{$request->search}%");
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
        $jobs  = $query->get();
        $data       = [
            'count' => $count,
            'jobs'  => $jobs
        ];
        return ok('Car Service Job list', $data);
    }

    /**
     * API of new create Car Service Job.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json $job
     */
    public function create(Request $request)
    {
        $request->validate([
            'car_service_id'        => 'required|exists:car_services,id',
            'user_id'               => 'required|exists:users,id',
            'service_type_id'       => 'required|exists:service_types,id',
        ]);
        $job = CarServiceJob::create($request->only('car_service_id', 'user_id', 'service_type_id'));
        return ok('Car Service Job created successfully!', $job);
    }

    /**
     * API to get Car Service Job with $id.
     *
     * @param  \App\CarServiceJob  $id
     * @return json $job
     */
    public function show($id)
    {
        $job = CarServiceJob::findOrFail($id);
        return ok('Car Service retrieved successfully', $job);
    }

    /**
     * API of Update Car Service Job Data.
     *
     * @param  \App\CarServiceJob  $id
     * @return json $job
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'car_service_id'        => 'required|exists:car_services,id',
            'user_id'               => 'required|exists:users,id',
            'service_type_id'       => 'required|exists:service_types,id',
        ]);
        $job = CarServiceJob::findOrFail($id);
        $job->update($request->only('car_service_id', 'user_id', 'service_type_id'));
        return ok('Car Service Job updated successfully!', $job);
    }

    /**
     * API of Delete Car Service Job data.
     *
     * @param  \App\CarServiceJob  $id
     * @return json
     */
    public function delete($id)
    {
        CarServiceJob::findOrFail($id)->delete();
        return ok('Car Service deleted successfully');
    }

    /**
     * API for Status of Car Service Job data.
     *
     * @param  \App\CarServiceJob  $id
     * @return json
     */
    public function status(Request $request, $id)
    {
        $request->validate([
            'status'          => 'required|in:In-Progress,Complete',
        ]);
        $job = CarServiceJob::findOrFail($id);
        if ($request->status == 'In-Progress') {
            dd($job->services()->status->get());
        }
        $job->update($request->only('status'));
        return ok('Car Service status updated successfully', $job);
    }
}
