<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <style>
        p {
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div>
        <p>Hey {{ $owner->first_name }},</p>
        <p>Myself {{ $order->user->first_name }} </p>
        <p>I want to Order Your Product of Garage</p>
        <h5>Order Details</h5>
        <table border="1">
            <thead>
                <th>Invoice Number</th>
                <th>Order Number</th>
                <th>Manufacture Date</th>
                <th>Stock Name</th>
                <th>Garage Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Tax</th>
                <th>Total Amount</th>
            </thead>
            @foreach ($order->stocks as $stocks)
                @php
                    $tax = ($stocks->price * $order->tax) / 100;
                @endphp
                <tr>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->order_id }}</td>
                    <td>{{ $stocks->manufacture_date }}</td>
                    <td>{{ $stocks->name }}</td>
                    <td>{{ $stocks->garage->name }}</td>
                    <td>{{ $stocks->price }}</td>
                    <td>{{ $invoice->quantity }}</td>
                    <td>{{ $invoice->tax }}</td>
                    <td>{{ ($stocks->price + $tax) * $order->quantity }}</td>
                </tr>
            @endforeach
        </table>
        <a href="{{ url('api/v1/invoice', $order->id) }}"><button class='btn btn-primary'>Invoice Download</button></a>
        <div>
</body>

</html>
