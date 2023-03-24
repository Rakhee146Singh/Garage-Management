<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <title>Reset Password</title>
    <style>
        .button {
            background-color: #008CBA;
            border: none;
            color: white;
            padding: 16px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            transition-duration: 0.4s;
            cursor: pointer;
        }

        .button2 {
            background-color: white;
            color: black;
            border: 2px solid #008CBA;
        }

        .button2:hover {
            background-color: #008CBA;
            color: white;
        }
    </style>
</head>

<body>
    <h1>Hello.. {{ auth()->user()->name }}</h1>
    <hr>
    <h4>You are receiving this email because we received a password reset request for your account.</h4>
    <p>We cannot simply send you your old password. A unique link to reset your password has been generated for you.
        This password reset link will expire in 60 minutes. </p>
    <h5><a href="http://127.0.0.1:8000/api/v1/reset-password/{{ $token }}"><button>Reset Password</button></a>
    </h5>
</body>

</html>
