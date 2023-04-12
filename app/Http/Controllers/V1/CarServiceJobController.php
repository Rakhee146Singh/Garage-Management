<?php

namespace App\Http\Controllers\V1;

use PDF;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CarServiceJob;
use Illuminate\Support\Carbon;
use App\Models\CustomerInvoice;
use App\Mail\CustomerInvoiceMail;
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
                'start_time'            => 'required|date_format:H:i',
                'end_time'              => 'required|date_format:H:i|after:start_time',
            ]
        );

        $user   = User::findOrFail($request->user_id);
        $start  = $request->start_time;
        $end    = $request->end_time;
        $status = $user->job()->whereIn('status', ['P', 'IP'])->whereBetween('start_time', [$start, $end])->get();

        /** Check Mechanic Is available or not by status else Create job.  */
        if ($status->count() > 0) {
            return ok('mechanic not available');
        } else {
            $job = CarServiceJob::create(
                $request->only(
                    'car_service_id',
                    'user_id',
                    'service_type_id',
                    'start_time',
                    'end_time'
                )
            );
        }
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
                'start_time'            => 'required|date_format:H:i',
                'end_time'              => 'required|date_format:H:i|after:start_time',
            ]
        );
        $job    = CarServiceJob::findOrFail($id);
        $user   = User::findOrFail($request->user_id);
        $start  = $request->start_time;
        $end    = $request->end_time;
        $status = $user->job()->whereIn('status', ['P', 'IP'])->whereBetween('start_time', [$start, $end])->get();

        /** Check Mechanic Is available or not by status else Update job. */
        if ($status->count() > 0) {
            return ok('mechanic not available');
        } else {
            $job->update(
                $request->only(
                    'car_service_id',
                    'user_id',
                    'service_type_id',
                    'start_time',
                    'end_time'
                )
            );
        }
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
                'extra_charges'   => 'nullable',
            ]
        );
        $job = CarServiceJob::findOrFail($id);

        /** Restrict no other mechanic can update status. */
        if (Auth::id() != $job->user_id) {
            return ok('User not valid');
        }

        $job->update($request->only('status'));

        /** Update status with relationship in Car Service table */
        $service            = $job->services;
        if ($request->status == 'IP') {
            $service->update($request->only('status'));
        } else {
            /** If user status is Complete than generate Invoice for customer by mechanic */
            $service->update($request->only('status'));
            $garage         = $job->user->garages->first();
            $user           = $service->cars->users;
            $total_amount   = $job->serviceType->price + $request->extra_charges;
            $invoice        = CustomerInvoice::create(
                [
                    'service_num'           => Str::random(6),
                    'car_service_job_id'    => $job->id,
                    'garage_id'             => $garage->id,
                    'extra_charges'         => $request->extra_charges,
                    'total_amount'          => $total_amount
                ]
            );
        }
        Mail::to($user->email)->send(new CustomerInvoiceMail($job, $user, $garage, $invoice));
        return ok('Car Service status updated successfully', $job);
    }

    /**
     * API to get Invoice with $id.
     *
     * @param  \App\CarServiceJob  $id
     * @return json
     */
    public function invoice($id)
    {
        $job = CarServiceJob::findOrFail($id);

        /** Fetching data from the relationships */
        $invoice    = $job->invoice;
        $user       = $job->services->cars->users;
        $garage     = $job->user->garages->first();
        $todayDate  = Carbon::now()->format('d-m-Y');

        /** Download Pdf of CustomerInvoice */
        $pdf        = PDF::loadView('customerInvoice_pdf', array('invoice' => $invoice, 'job' => $job, 'user' => $user, 'garage' => $garage));
        return $pdf->download('invoice' . $job->id . '-' . $todayDate . '.pdf');
    }
}
