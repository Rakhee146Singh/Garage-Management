<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mechanic Service</title>
</head>

<body>
    <p> Hello.. {{ $job->users->first_name }}</p>
    <p> You got mail for Car Service Assign to you</p>
    <p> Car Service ID: {{ $job->car_service_id }} </p>
    <p> Service Type Name: {{ $job->serviceType->name }} </p>

    <p>Customer Details Are:</p>
    <p>Customer Name: {{ $job->services->cars->users->first_name }}</p>
    <p>Phone: {{ $job->services->cars->users->phone }}</p>
    <p>Address: {{ $job->services->cars->users->address1 }}</p>

    <p>Car Details Are </p>
    <p>Company Name: {{ $job->services->cars->company_name }}</p>
    <p>Model Name: {{ $job->services->cars->model_name }}</p>
    <p>Manufacturing Date: {{ $job->services->cars->manufacturing_year }}</p>
</body>

</html>
