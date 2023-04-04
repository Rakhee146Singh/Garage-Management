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
                <th>Garage Name</th>
                <th>Stock Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Tax</th>
                <th>Total Amount</th>
                <th>Manufacture Date</th>
            </thead>
            <tr>
                <td>{{ $stock->garage->name }}</td>
                <td>{{ $stock->name }}</td>
                <td>{{ $stock->price }}</td>
                <td>{{ $order->quantity }}</td>
                <td>{{ $order->tax }}</td>
                <td>{{ $order->total_amount }}</td>
                <td>{{ $stock->manufacture_date }}</td>
            </tr>
        </table>
        <a href="{{ url('api/v1/approve', $order->id) }}"><button class='btn btn-primary'>Accept</button></a>
        <a href="{{ url('api/v1/reject', $order->id) }}"><button class='btn btn-danger'>Reject</button></a>
        <div>
</body>

</html>
