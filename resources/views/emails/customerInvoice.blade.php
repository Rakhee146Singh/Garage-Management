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
        <p>Hey, {{ $user->first_name }} </p>
        <p>Myself {{ $job->user->first_name }},</p>
        <p>I have Completed your Car Service. <br> Please do collect your car from the garage</p>
        <h5>Service Details</h5>
        <table border="1">
            <thead>
                <th>Service Number</th>
                <th>Service Type Name</th>
                <th>Garage Name</th>
                <th>Car Name</th>
                <th>Model Name</th>
                <th>Price</th>
                <th>Extra Charges</th>
                <th>Total Amount</th>
            </thead>
            <tr>
                <td>{{ $invoice->service_num }}</td>
                <td>{{ $job->serviceType->name }}</td>
                <td>{{ $garage->name }}</td>
                <td>{{ $job->services->cars->company_name }}</td>
                <td>{{ $job->services->cars->model_name }}</td>
                <td>{{ $job->serviceType->price }}</td>
                <td>{{ $invoice->extra_charges ? $invoice->extra_charges : 0 }}</td>
                <td>{{ $invoice->total_amount }}</td>
            </tr>
        </table>
        <a href="{{ url('api/v1/service/invoice', $job->id) }}"><button class='btn btn-primary'>Invoice
                Download</button></a>
        <div>
</body>

</html>
