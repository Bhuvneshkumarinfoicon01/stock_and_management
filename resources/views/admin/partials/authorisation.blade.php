<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Access Denied</title>
    <style>
        body {
            background-color: #f8d7da;
            color: #721c24;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .error-box {
            text-align: center;
            background-color: #f5c6cb;
            border: 1px solid #f1b0b7;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .error-box h1 {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .error-box p {
            font-size: 20px;
        }

        .error-box a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #721c24;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        .error-box a:hover {
            background-color: #501316;
        }
    </style>
</head>

<body>
    <div class="error-box">
        <h1>403 - Access Denied</h1>
        <p>Sorry, you do not have permission to access this page.</p>
        <a href="{{ url('/') }}">Return to Home</a>
    </div>
</body>

</html>