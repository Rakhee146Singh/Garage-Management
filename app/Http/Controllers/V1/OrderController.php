<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use App\Models\Order;
use App\Models\Stock;
use App\Mail\OrderMail;
use App\Models\Invoice;
use App\Mail\InvoiceMail;
use App\Models\GarageUser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\OrderCancelMail;
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
                'stock_id'          => 'required|exists:stocks,id',
                'quantity'          => 'required|integer|max:50',
                'tax'               => 'required|integer|max:100',
            ]
        );

        $stock = Stock::findOrFail($request->stock_id);
        $tax = $request->tax / 100;
        $total_amount = ($stock->price * $request->quantity) + $tax;
        $order = Order::create(
            $request->only(
                'user_id',
                'garage_id',
                'stock_id',
                'quantity',
                'tax'
            ) +
                [
                    'total_amount' => $total_amount
                ]
        );
        /** Sending mail to Garage owner with Customer Car details and Car Service Id*/
        $owner_data = GarageUser::where('garage_id', $request->garage_id)->where('is_owner', true)->first();
        $owner      = User::findOrFail($owner_data->user_id);
        Mail::to($owner->email)->send(new OrderMail($owner, $order, $stock));

        return ok('Order created successfully!', $order->load('stock', 'user'));
    }

    /**
     * API to get Order with $id.
     *
     * @param  \App\Order  $id
     * @return json $order
     */
    public function show($id)
    {
        $order = Order::with('stock', 'user', 'invoice')->findOrFail($id);
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
        if ($order->status == 'R') {
            return "Your Order is already Rejected.";
        }
        if ($order->status == 'A') {
            return "Your Order is already Accepted.";
        } else {
            $order->update(['status' => 'A']);
        }
        $invoice = Invoice::create(
            [
                'order_id'          => $order->id,
                'stock_id'          => $order->stock_id,
                'user_id'           => $order->user_id,
                'garage_id'         => $order->garage_id,
                'invoice_number'    => Str::random(6),
                'quantity'          => $order->quantity,
                'tax'               => $order->tax,
                'total_amount'      => $order->total_amount
            ]
        );

        /** Sending mail to Garage owner with Customer Car details and Car Service Id*/
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
    public function reject(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        if ($order->status == 'A') {
            return "Your Order is already Accepted.";
        }
        if ($order->status == 'R') {
            return "Your Order is already Rejected.";
        } else {
            $order->update(['status' => 'R']);
        }

        /** Sending mail to Garage owner with Customer Car details and Car Service Id*/
        $owner_data = GarageUser::where('garage_id', $order->garage_id)->where('is_owner', true)->first();
        $owner      = User::findOrFail($owner_data->user_id);
        Mail::to($owner->email)->send(new OrderCancelMail($owner, $order));

        return ok('Your Order Is been Cancelled');
    }
}
