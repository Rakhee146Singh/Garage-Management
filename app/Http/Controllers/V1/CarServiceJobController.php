<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\CarServiceJob;
use App\Mail\MechanicServiceMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CarServiceJobController extends Controller
{
    /**
     * API of new create Car Service Job.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json $job
     */
    public function create(Request $request)
    {
        $request->validate(
            [
                'car_service_id'        => 'required|exists:car_services,id',
                'user_id'               => 'required|exists:users,id',
                'service_type_id'       => 'required|exists:service_types,id',
            ]
        );

        $user = User::findOrFail($request->user_id);
        $job = CarServiceJob::create(
            $request->only(
                'car_service_id',
                'user_id',
                'service_type_id'
            )
        );
        Mail::to($user->email)->send(new MechanicServiceMail($job));
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
        $request->validate(
            [
                'car_service_id'        => 'required|exists:car_services,id',
                'user_id'               => 'required|exists:users,id',
                'service_type_id'       => 'required|exists:service_types,id',
            ]
        );
        $job = CarServiceJob::findOrFail($id);
        $user = User::findOrFail($request->user_id);

        $job->update(
            $request->only(
                'car_service_id',
                'user_id',
                'service_type_id'
            )
        );
        Mail::to($user->email)->send(new MechanicServiceMail($job));
        return ok('Car Service Job updated successfully!', $job);
    }

    /**
     * API for Status of Car Service Job data.
     *
     * @param  \App\CarServiceJob  $id
     * @return json $job
     */
    public function status(Request $request, $id)
    {
        $request->validate(
            [
                'status'          => 'required|in:IP,C',
            ]
        );
        $job = CarServiceJob::findOrFail($id);
        $job->update($request->only('status'));

        if (Auth::id() == $job->user_id) {
            if ($request->status == 'IP' || $request->status == 'C') {
                $service = $job->services;
                $service->update($request->only('status'));
            }
        } else {
            return ok('User not valid');
        }
        return ok('Car Service status updated successfully', $job);
    }
}
