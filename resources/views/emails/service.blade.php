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

        .signature {
            font-style: italic;
        }
    </style>
</head>

<body>
    <div>
        <p>Hey {{ $owner->first_name }},</p>
        <p>Myself {{ $car->users->first_name }} </p>
        <p>I want to get my Car Service</p>
        <p>My Car Service ID:
            @foreach ($services as $service)
                {{ $service->id }} &nbsp;,&nbsp;
            @endforeach
        </p>
        <h5>Car Details</h5>
        <table border="1">
            <thead>
                <th>Service Name</th>
                <th>Company Name</th>
                <th>Model Name</th>
                <th>Manufacturing Year</th>
            </thead>
            @foreach ($car->types as $service)
                <tr>
                    <td>{{ $service->name }}</td>
                    <td>{{ $car->company_name }}</td>
                    <td>{{ $car->model_name }}</td>
                    <td>{{ $car->manufacturing_year }}</td>
                </tr>
            @endforeach
        </table>
        <div>
</body>

</html>
