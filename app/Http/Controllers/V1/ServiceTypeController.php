<?php

namespace App\Http\Controllers\V1;

use App\Models\ServiceType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceTypeController extends Controller
{
    /**
     * API of listing ServiceType data.
     *
     * @return json $services
     */
    public function list(Request $request)
    {
        $request->validate(
            [
                'search'        => 'nullable|string',
                'sortOrder'     => 'nullable|in:asc,desc',
                'sortField'     => 'nullable|string',
                'perPage'       => 'nullable|integer',
                'currentPage'   => 'nullable|integer'
            ]
        );
        $query = ServiceType::query(); //query

        /* Searching */
        if (isset($request->search)) {
            $query = $query->where("name", "LIKE", "%{$request->search}%");
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
        $data           = [
            'count'     => $count,
            'services'  => $query->get()
        ];
        return ok('Service Type list', $data);
    }

    /**
     * API of new create ServiceType.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json $service
     */
    public function create(Request $request)
    {
        $request->validate(
            [
                'name'         => 'required|alpha|max:30',
            ]
        );
        $service = ServiceType::create($request->only('name'));
        return ok('Service Type created successfully!', $service);
    }

    /**
     * API to get ServiceType with $id.
     *
     * @param  \App\ServiceType  $id
     * @return json $service
     */
    public function show($id)
    {
        $service = ServiceType::findOrFail($id);
        return ok('Service Type retrieved successfully', $service);
    }

    /**
     * API of Update ServiceType Data.
     *
     * @param  \App\ServiceType  $id
     * @return json $service
     */
    public function update(Request $request, $id)
    {
        $service = ServiceType::findOrFail($id);
        $request->validate(
            [
                'name'         => 'required|alpha|max:30',
            ]
        );
        $service->update($request->only('name'));
        return ok('Service Type updated successfully!', $service);
    }

    /**
     * API of Delete ServiceType data.
     *
     * @param  \App\ServiceType  $id
     * @return json
     */
    public function delete($id)
    {
        ServiceType::findOrFail($id)->delete();
        return ok('Service Type deleted successfully');
    }
}
