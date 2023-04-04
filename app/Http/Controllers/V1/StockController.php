<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Stock;

class StockController extends Controller
{
    /**
     * API of listing Stock data.
     *
     * @return json $stocks
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
        $query = Stock::query(); //query

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
            'stocks'    => $query->get()
        ];
        return ok('Stocks list', $data);
    }

    /**
     * API of new create Stock.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json $stock
     */
    public function create(Request $request)
    {
        $request->validate(
            [
                'garage_id'         => 'required|exists:garages,id',
                'name'              => 'required|string|max:50',
                'description'       => 'required|string|max:100',
                'price'             => 'required|integer',
                'quantity'          => 'required|integer',
                'manufacture_date'  => 'required|date|date_format:Y-m-d',
            ]
        );
        $stock = Stock::create(
            $request->only(
                'garage_id',
                'name',
                'description',
                'price',
                'quantity',
                'manufacture_date'
            )
        );
        return ok('Stock created successfully!', $stock->load('garage'));
    }

    /**
     * API to get Stock with $id.
     *
     * @param  \App\Stock  $id
     * @return json $stock
     */
    public function show($id)
    {
        $stock = Stock::with('garage')->findOrFail($id);
        return ok('Stock retrieved successfully', $stock);
    }

    /**
     * API of Update Stock Data.
     *
     * @param  \App\Stock  $id
     * @return json $stock
     */
    public function update(Request $request, $id)
    {
        $stock = Stock::findOrFail($id);
        $request->validate(
            [
                'garage_id'         => 'required|exists:garages,id',
                'name'              => 'required|string|max:50',
                'description'       => 'required|string|max:100',
                'price'             => 'required|integer',
                'quantity'          => 'required|integer',
                'manufacture_date'  => 'required|date|date_format:Y-m-d',
            ]
        );
        $stock->update(
            $request->only(
                'garage_id',
                'name',
                'description',
                'price',
                'quantity',
                'manufacture_date'
            )
        );
        return ok('Stock updated successfully!', $stock->load('garage'));
    }

    /**
     * API of Delete Stock data.
     *
     * @param  \App\Stock  $id
     * @return json
     */
    public function delete($id)
    {
        $stock = Stock::findOrFail($id);
        $stock->delete();
        return ok('Stock deleted successfully');
    }
}
