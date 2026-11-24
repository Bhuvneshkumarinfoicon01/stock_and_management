<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="author" content="" />
    <meta name="robots" content="noindex, nofollow" />
    <title>Accounting and Stock Management System - Reset Password</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('admin/assets/img/favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/css/bootstrap-datetimepicker.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/css/dataTables.bootstrap5.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/css/boxicons.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/css/select2.min.css') }}" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('admin/assets/css/style.css') }}" />

    <style>
        body.account-page {
            background: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .main-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 2rem;
            background: #fff;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .login-content h2 {
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #333;
            text-align: center;
        }
        .form-group label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: block;
            color: #555;
        }
        .form-control {
            height: 44px;
            padding: 0.6rem 1rem;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            transition: border-color 0.3s ease;
        }
        .form-control:focus {
            border-color: #fd7e14;;
            box-shadow: none;
            outline: none;
        }
        button.btn-primary {
            width: 100%;
            padding: 0.75rem;
            font-size: 1.1rem;
            border-radius: 4px;
            border: none;
            background-color: #fd7e14;;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 1.5rem;
        }
        button.btn-primary:hover {
            background-color: #fd7e14;;
        }
    </style>
</head>
<body class="account-page">
    <div class="main-wrapper">
        <div class="login-content">
            <h2>Reset Password</h2>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="form-group">
                    <label for="password">New Password</label>
                    <div class="pass-group" style="position: relative;">
                        <input id="password" type="password" name="password" class="form-control pass-input"  autocomplete="new-password" autofocus />
                        <span class="fas toggle-password fa-eye-slash" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></span>
                    </div>
                    @error('password')
                    <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <div class="pass-group" style="position: relative;">
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control pass-input" autocomplete="new-password" />
                        <span class="fas toggle-password fa-eye-slash" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></span>
                    </div>
                     @error('password_confirmation')
                    <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Reset Password</button>
            </form>
        </div>
    </div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePasswords = document.querySelectorAll('.toggle-password');

        togglePasswords.forEach(function(toggle) {
            toggle.addEventListener('click', function () {
                const input = this.previousElementSibling;
                if (input && input.classList.contains('pass-input')) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.classList.remove('fa-eye-slash');
                        this.classList.add('fa-eye');
                    } else {
                        input.type = 'password';
                        this.classList.remove('fa-eye');
                        this.classList.add('fa-eye-slash');
                    }
                }
            });
        });
    });
</script>
</body>
</html>
