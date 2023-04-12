<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CarService;

class CarServiceController extends Controller
{
    /**
     * API for Status of Car Service data.
     *
     * @param  \App\CarService  $id
     * @return json $carService
     */
    public function status(Request $request, $id)
    {
        $request->validate(
            [
                'status'          => 'required|in:DE,D',
            ]
        );

        if (auth()->user()->type != 'owner') {
            return ok('User Invalid');
        }

        $carService = CarService::findOrFail($id);
        $carService->update($request->only('status'));
        return ok('Car Service status updated successfully', $carService->load('jobs'));
    }
}
