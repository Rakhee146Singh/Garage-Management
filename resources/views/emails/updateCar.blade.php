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
        <p>Myself {{ $user->first_name }} </p>
        <p>I have Updated My Car Service</p>
        <p>My Updated Car Service ID:
            @foreach ($services as $service)
                {{ $service->id }} &nbsp;,&nbsp;
            @endforeach
        </p>
        <h5>Updated Car Details</h5>
        <table border="1">
            <thead>
                <th>Service Name</th>
                <th>Company Name</th>
                <th>Model Name</th>
                <th>Manufacturing Year</th>
            </thead>
            {{-- @foreach ($cars as $car) --}}
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
