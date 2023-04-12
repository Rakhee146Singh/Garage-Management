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
            <p class="m-0 pt-5 text-bold w-100"> Invoice Id - <span class="gray-color">{{ $invoice->id }}</span></p>
            <p class="m-0 pt-5 text-bold w-100">Service Id - <span class="gray-color"> {{ $invoice->service_num }}</span>
            </p>
            <p class="m-0 pt-5 text-bold w-100">Service Date - <span class="gray-color">{{ $job->created_at }}</span>
            </p>
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
                        <p>{{ $user->first_name }},</p>
                        <p>{{ $user->address1 }},</p>
                        <p>{{ $user->address2 }},</p>
                        <p>{{ $user->zipcode }},</p>
                        <p>{{ $user->phone }}</p>
                    </div>
                </td>
                <td>
                    <div class="box-text">
                        <p>{{ $job->user->first_name }},</p>
                        <p>{{ $job->user->address1 }},</p>
                        <p>{{ $job->user->address2 }},</p>
                        <p>{{ $job->user->zipcode }},</p>
                        <p>{{ $job->user->phone }}</p>
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
                    <th>Service Number</th>
                    <th>Service Type Name</th>
                    <th>Garage Name</th>
                    <th>Car Name</th>
                    <th>Model Name</th>
                    <th>Price</th>
                    <th>Extra Charges</th>
                    <th>Total Amount</th>
                </tr>

                {{-- @foreach ($order->stocks as $stocks)
                    @php
                        $tax = ($stocks->price * $order->tax) / 100;
                    @endphp --}}
                <tr align="center">
                    <td>{{ $invoice->service_num }}</td>
                    <td>{{ $job->serviceType->name }}</td>
                    <td>{{ $garage->name }}</td>
                    <td>{{ $job->services->cars->company_name }}</td>
                    <td>{{ $job->services->cars->model_name }}</td>
                    <td>{{ $job->serviceType->price }}</td>
                    <td>{{ $invoice->extra_charges ? $invoice->extra_charges : 0 }}</td>
                    <td>{{ $invoice->total_amount }}</td>
                </tr>
                {{-- @endforeach --}}
                <tr>
                    <td colspan="8">
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
