<!DOCTYPE html>
<html>

<head>
    <title>Generate Invoice PDF</title>
</head>
<style type="text/css">
    body {
        font-family: 'Roboto Condensed', sans-serif;
    }

    .m-0 {
        margin: 0px;
    }

    .p-0 {
        padding: 0px;
    }

    .pt-5 {
        padding-top: 5px;
    }

    .mt-10 {
        margin-top: 10px;
    }

    .text-center {
        text-align: center !important;
    }

    .w-100 {
        width: 100%;
    }

    .w-50 {
        width: 50%;
    }

    .w-85 {
        width: 85%;
    }

    .w-15 {
        width: 15%;
    }

    .logo img {
        width: 200px;
        height: 60px;
    }

    .gray-color {
        color: #39408c;
    }

    .text-bold {
        font-weight: bold;
    }

    .border {
        border: 1px solid black;
    }

    table tr,
    th,
    td {
        border: 1px solid #000000;
        border-collapse: collapse;
        padding: 7px 8px;
    }

    table tr th {
        background: #9f9ab5;
        font-size: 15px;
    }

    table tr td {
        font-size: 13px;
    }

    table {
        border-collapse: collapse;
    }

    .box-text p {
        line-height: 10px;
    }

    .float-left {
        float: left;
    }

    .total-part {
        font-size: 16px;
        line-height: 12px;
    }

    .total-right p {
        padding-right: 20px;
    }
</style>

<body>
    <div class="head-title">
        <h1 class="text-center m-0 p-0">Invoice</h1>
    </div>
    <div class="add-detail mt-10">
        <div class="w-50 float-left mt-10">
            <p class="m-0 pt-5 text-bold w-100"> Invoice Id - <span
                    class="gray-color">{{ $invoice->invoice_number }}</span></p>
            <p class="m-0 pt-5 text-bold w-100">Order Id - <span class="gray-color"> {{ $invoice->order_id }}</span></p>
            <p class="m-0 pt-5 text-bold w-100">Order Date - <span class="gray-color">{{ $order->created_at }}</span></p>
        </div>
        <div style="clear: both;"></div>
    </div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-50">From</th>
                <th class="w-50">To</th>
            </tr>
            <tr>
                <td>
                    <div class="box-text">
                        <p>{{ $order->user->first_name }},</p>
                        <p>{{ $order->user->address1 }},</p>
                        <p>{{ $order->user->address2 }},</p>
                        <p>{{ $order->user->zipcode }},</p>
                        <p>{{ $order->user->phone }}</p>
                    </div>
                </td>
                <td>
                    <div class="box-text">
                        <p>{{ $owner->first_name }},</p>
                        <p>{{ $owner->address1 }},</p>
                        <p>{{ $owner->address2 }},</p>
                        <p>{{ $owner->zipcode }},</p>
                        <p>{{ $owner->phone }}</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-50">Payment Method</th>
                <th class="w-50">Shipping Method</th>
            </tr>
            <tr>
                <td>Cash On Delivery</td>
                <td>Free Shipping - Free Shipping</td>
            </tr>
        </table>
    </div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tbody>
                <tr>
                    <th class="w-50">Garage Name</th>
                    <th class="w-50">Stock Name</th>
                    <th class="w-50">Manufacture Date</th>
                    <th class="w-50">Price</th>
                    <th class="w-50">Quantity</th>
                    <th class="w-50">Tax</th>
                    <th class="w-50">Total Amount</th>
                </tr>

                @foreach ($order->stocks as $stock)
                    @php
                        $tax = ($stock->price * $order->tax) / 100;
                    @endphp
                    <tr align="center">
                        <td>{{ $stock->garage->name }}</td>
                        <td>{{ $stock->name }}</td>
                        <td>{{ $stock->manufacture_date }}</td>
                        <td>{{ $stock->price }}</td>
                        <td>{{ $stock->pivot->quantity }}</td>
                        <td>{{ $invoice->tax }}</td>
                        <td>{{ ($stock->price + $tax) * $stock->pivot->quantity }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="7">
                        <div class="total-part">
                            <div class="total-left w-85 float-left" align="right">
                                <p>Sub Total</p>
                                <p>Tax (18%)</p>
                                <p>Total Payable</p>
                            </div>
                            <div class="total-right w-15 float-left text-bold" align="right">
                                <p>{{ $invoice->total_amount }}</p>
                                @php
                                    $tax = ($invoice->total_amount * 18) / 100;
                                @endphp
                                <p>{{ $tax }}</p>
                                <p>{{ $invoice->total_amount + $tax }}</p>
                            </div>
                            <div style="clear: both;"></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</html>
