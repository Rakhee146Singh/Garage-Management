<?php

namespace App\Http\Controllers\V1;

use PDF;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Stock;
use App\Mail\OrderMail;
use App\Models\Invoice;
use App\Mail\InvoiceMail;
use App\Models\GarageUser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\OrderCancelMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    /**
     * API of listing Order data.
     *
     * @return json $orders
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
        $query = Order::query(); //query

        /** Listing Stocks and User Details to Owner */
        if (auth()->user()->type == 'owner') {
            $query = $query->with('stocks', 'user');
        }

        /* Searching */
        if (isset($request->search)) {
            $query = $query->where("status", "LIKE", "%{$request->search}%");
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
        return ok('Orders list', $data);
    }

    /**
     * API of new create Order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json $order
     */
    public function create(Request $request)
    {
        $request->validate(
            [
                'user_id'           => 'required|exists:users,id',
                'garage_id'         => 'required|exists:garages,id',
                'quantity'          => 'required|integer|max:50',
                'tax'               => 'required|integer|max:100',
                'stock_id.*'        => 'required|exists:stocks,id',
            ]
        );

        $garage = GarageUser::where('garage_id', $request->garage_id)->where('is_owner', true)->first();
        $user = User::findOrFail($request->user_id);
        if (($user->id == $garage->user_id) && auth()->user()->type == 'owner') {
            return ok('You are not allowed to order');
        }

        /** Restrict user other than owner */
        if ($user->type != 'owner') {
            return ok('User Not Valid');
        }

        /** Multiple Stock Order by Owner */
        $stocks = [];
        $total_amount = 0;
        foreach ($request->stock_id as $stock_id) {
            $stock = Stock::findOrFail($stock_id);
            $tax = ($stock->price * $request->tax) / 100;
            $total_amount += ($stock->price + $tax) * $request->quantity;
            array_push($stocks, $stock);
        }

        $order = Order::create(
            $request->only(
                'user_id',
                'garage_id',
                'quantity',
                'tax'
            ) +
                [
                    'total_amount'  => $total_amount,
                ]
        );
        $order->stocks()->attach($request->stock_id);

        /** Sending mail to Garage owner with Order with Total Amount */
        // $owner_data = GarageUser::where('garage_id', $request->garage_id)->where('is_owner', true)->first();
        // $owner      = User::findOrFail($owner_data->user_id);
        // Mail::to($owner->email)->send(new OrderMail($owner, $order, $stocks));

        return ok('Order created successfully!', $order->load('stocks', 'user'));
    }

    /**
     * API to get Order with $id.
     *
     * @param  \App\Order  $id
     * @return json $order
     */
    public function show($id)
    {
        $order = Order::with('stocks', 'user', 'invoice')->findOrFail($id);
        return ok('Order retrieved successfully', $order);
    }

    /**
     * API to get Order with $id.
     *
     * @param  \App\Order  $id
     * @return json $invoice
     */
    public function approve($id)
    {
        $order = Order::findOrFail($id);

        /** Check Status Accepted Or Rejected */
        if ($order->status == 'R') {
            return "Your Order is already Rejected.";
        }
        if ($order->status == 'A') {
            return "Your Order is already Accepted.";
        } else {
            $order->update(['status' => 'A']);
        }

        /** Invoice generate when order accepted by Owner. */
        $invoice = Invoice::create(
            [
                'order_id'          => $order->id,
                'user_id'           => $order->user_id,
                'garage_id'         => $order->garage_id,
                'invoice_number'    => Str::random(6),
                'quantity'          => $order->quantity,
                'tax'               => $order->tax,
                'total_amount'      => $order->total_amount
            ]
        );

        /** Sending mail to Garage owner with Order details and Generate invoice */
        $owner_data = GarageUser::where('garage_id', $order->garage_id)->where('is_owner', true)->first();
        $owner      = User::findOrFail($owner_data->user_id);
        Mail::to($owner->email)->send(new InvoiceMail($owner, $order, $invoice));

        return ok('Invoice Generated successfully', $invoice);
    }

    /**
     * API to get Order with $id.
     *
     * @param  \App\Order  $id
     * @return json $order
     */
    public function reject($id)
    {
        $order = Order::findOrFail($id);

        /** Check Status Accepted Or Rejected */
        if ($order->status == 'A') {
            return "Your Order is already Accepted.";
        }
        if ($order->status == 'R') {
            return "Your Order is already Rejected.";
        } else {
            $order->update(['status' => 'R']);
        }

        /** Order Reject for Multiple stock created when Orderded */
        $stocks = [];
        foreach ($order->stocks as $stock) {
            $stock = Stock::where('id', $stock->id)->first();
            array_push($stocks, $stock);
        }

        /** Sending mail to Garage owner with Customer Car details and Car Service Id*/
        $owner_data = GarageUser::where('garage_id', $order->garage_id)->where('is_owner', true)->first();
        $owner      = User::findOrFail($owner_data->user_id);
        Mail::to($owner->email)->send(new OrderCancelMail($owner, $order, $stocks));
        return ok('Your Order Is been Cancelled');
    }

    /**
     * API to get Invoice with $id.
     *
     * @param  \App\Order  $id
     * @return json
     */
    public function invoice($id)
    {
        $order = Order::findOrFail($id);
        $invoice = $order->invoice;

        /** Get data of Owner */
        $owner_data = GarageUser::where('garage_id', $order->garage_id)->where('is_owner', true)->first();
        $owner      = User::findOrFail($owner_data->user_id);
        $todayDate = Carbon::now()->format('d-m-Y');

        /** Download Pdf of Invoice */
        $pdf = PDF::loadView('invoice_pdf', array('invoice' => $invoice, 'order' => $order, 'owner' => $owner));
        return $pdf->download('invoice' . $order->id . '-' . $todayDate . '.pdf');
    }
}
