{{-- <x-mail::message>
# Hello

The body of your message.

<x-mail::button :url="''">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message> --}}

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
        <p>I want to get my Car Service</p>
        <h5>Car Details</h5>
        <table border="1">
            <thead>
                <th>Company Name</th>
                <th>Model Name</th>
                <th>Manufacturing Year</th>
            </thead>
            @foreach ($cars as $car)
                <tr>
                    <td>{{ $car->id }}</td>
                    <td>{{ $car->company_name }}</td>
                    <td>{{ $car->model_name }}</td>
                    <td>{{ $car->manufacturing_year }}</td>
                </tr>
            @endforeach
        </table>
        <br>
        <br>
        <h5>Service Type Details</h5>
        <table border="1">
            <thead>
                <th>Service Type</th>
            </thead>
            @foreach ($user->service as $services)
                <tr>
                    <td>{{ $services->name }}</td>
                </tr>
            @endforeach
        </table>
        <p class="signature">Mailtrap</p>
        <div>
</body>

</html>
