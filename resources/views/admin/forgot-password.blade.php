<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="author" content="">
	<meta name="robots" content="noindex, nofollow">
	<title>Accounting and Stock Management System</title>
	<script src="{{asset('admin/assets/js/theme-script.js')}}"></script>
	<link rel="shortcut icon" type="image/x-icon" href="{{asset('admin/assets/img/favicon.png')}}">
	<link rel="stylesheet" href="{{asset('admin/assets/css/bootstrap.min.css')}}">
	<link rel="stylesheet" href="{{asset('admin/assets/css/bootstrap-datetimepicker.min.css')}}">
	<link rel="stylesheet" href="{{asset('admin/assets/css/dataTables.bootstrap5.min.css')}}">
	<link rel="stylesheet" href="{{asset('admin/assets/css/animate.css')}}">
	<link rel="stylesheet" href="{{asset('admin/assets/css/boxicons.min.css')}}">
	<link rel="stylesheet" href="{{asset('admin/assets/css/select2.min.css')}}">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
	<link rel="stylesheet" href="{{asset('admin/assets/css/style.css')}}">
</head>
    <body class="account-page">
        <div class="main-wrapper">
            <div class="account-content">
                <div class="login-wrapper bg-img">
                    <div class="login-content">
                       <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                            <div class="login-userset">
                                <a href="#" class="login-logo">
                                    <h1>
                                        Accounting and Stock Management System
                                    </h1>
                                </a>
                                <div class="login-userheading">
                                    <h3>Forgot Password</h3>
                                    <h4>If you forgot your password, well, then we’ll email you instructions to reset your password.</h4>
                                </div>
                                <div class="form-login mb-3">
                                    <label class="form-label">Email Address</label>
                                    <div class="form-addons">
                                        <input type="text" class="form-control" name="email" required />
                                         <img src="{{ asset('admin/assets/img/icons/mail.svg') }}" alt="img">
                                    </div>
                                </div>
                                <div class="form-login">
                                    <button type="submit" class="btn btn-login">Sign In</button>
                                </div>
                                <div class="signinform">
                                    <h4>Return to
                                        <a href="{{route('login_view')}}" class="hover-a">Login</a>
                                    </h4>
                                </div>
                            </div>
                        </form>
                        @if (session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </body>
</html>