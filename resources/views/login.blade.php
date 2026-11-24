

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

	<!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="{{asset('admin/assets/img/favicon.png')}}">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="{{asset('admin/assets/css/bootstrap.min.css')}}">

	<!-- Datetimepicker CSS -->
	<link rel="stylesheet" href="{{asset('admin/assets/css/bootstrap-datetimepicker.min.css')}}">

	<!-- DataTables CSS -->
	<link rel="stylesheet" href="{{asset('admin/assets/css/dataTables.bootstrap5.min.css')}}">

	<!-- animation CSS -->
	<link rel="stylesheet" href="{{asset('admin/assets/css/animate.css')}}">

	<!-- animation CSS -->
	<link rel="stylesheet" href="{{asset('admin/assets/css/boxicons.min.css')}}">

	<!-- Select2 CSS -->
	<link rel="stylesheet" href="{{asset('admin/assets/css/select2.min.css')}}">

	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">


	<!-- Main CSS -->
	<link rel="stylesheet" href="{{asset('admin/assets/css/style.css')}}">

</head>
    <body class="account-page">

        <div class="main-wrapper">
			<div class="account-content">
				<div class="login-wrapper bg-img">
                    <div class="login-content">



                        <form action="{{ rtrim(route('login'), '/') . '/' }}" method="post">
                            @csrf
                            <div class="login-userset">
                                <a href="#" class="login-logo">
                                    <h1>Accounting and Stock Management System</h1>
                                </a>
                               <div class="login-userheading">
                                   <h3>Sign In</h3>
                                   <h4>Access the Accounting and Stock Management System panel using your email and passcode.</h4>
                                    @if (Session::has('success'))
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <span>{{ Session::get('success') }}</span>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    @if (Session::has('error'))
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <span>{{ Session::get('error') }}</span>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    @if ($errors->has('error') || request()->query('invalid'))
                                        <div class="alert alert-danger">
                                            {{ $errors->first('error') ?: 'Invalid email or password.' }}
                                        </div>
                                    @endif
                               </div>
                               <div class="form-login mb-3">
                                    <label class="form-label">Email Address</label>
                                    <div class="form-addons">
                                        <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}">
                                        <img src="{{ asset('admin/assets/img/icons/mail.svg') }}" alt="img">
                                    </div>
                                    @error('email')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-login mb-3">
                                    <label class="form-label">Password</label>
                                    <div class="pass-group">
                                        <input type="password" class="pass-input form-control @error('password') is-invalid @enderror" name="password">
                                        <span class="fas toggle-password fa-eye-slash"></span>
                                    </div>
                                    @error('password')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                               <div class="form-login authentication-check">
                                   <div class="row">
                                       <div class="col-12 d-flex align-items-center justify-content-between">
                                           <div class="custom-control custom-checkbox">
                                               <label class="checkboxs ps-4 mb-0 pb-0 line-height-1">
                                                   <input type="checkbox" class="form-control" name="remember">
                                                   <span class="checkmarks"></span>Remember me
                                               </label>
                                           </div>
                                           <div class="text-end">
                                               <a class="forgot-link" href="{{route('forgot-password')}}">Forgot Password?</a>
                                           </div>
                                       </div>                                    
                                   </div>
                               </div>
                               <div class="form-login">
                                   <button type="submit" class="btn btn-login">Sign In</button>
                               
                           </div>
                        </form>
                    </div>
                </div>
			</div>
        </div>

    </body>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.querySelector('.toggle-password');
            const passwordInput = document.querySelector('.pass-input');
    
            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function () {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            }
        });
    </script>
    
    </html>

       

	
