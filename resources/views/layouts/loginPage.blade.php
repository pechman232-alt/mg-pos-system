<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/assets/css/login_style.min.css') }}">

    <link rel="shortcut icon" href="{{ asset('/assets/images/logo.png') }}">

    <title>LoginPage</title>
    <style>
        body {
            /* font-family: "Karla", sans-serif; */
            font-family: "Kantumruy Pro", sans-serif;
            background-color: #f5e2c5;
            min-height: 100vh;
        }
        .brand-wrapper {
            margin-bottom: 19px; 
        }

        .brand-wrapper .logo {
            height: 130px; 
        }

        .login-card {
            border: 0;
            border-radius: 10.5px;
            box-shadow: 0 10px 30px 0 rgba(172, 168, 168, 0.43);
            overflow: hidden; 
        }

        .login-card-img {
            border-radius: 0;
            position: absolute;
            width: 100%;
            height: 100%;
            -o-object-fit: cover;
            object-fit: cover; 
        }

        .background-img {
            position: absolute;
            width: 100%;
            height: 100%;
            -o-object-fit: cover;
            object-fit: cover;
            filter: blur(3.5px);
            -webkit-filter: blur(3.5px);
            opacity: 60%;
        }

        .login-card .card-body {
            padding: 85px 60px 60px; 
            text-align: -webkit-center;
        }

        @media (max-width: 422px) {
            .login-card .card-body {
                padding: 35px 24px; 
            } 
        }

        .login-card-description {
            font-size: 18px;
            color: #000;
            font-weight: 600;
            margin-bottom: 23px; 
        }

        .login-card form {
            max-width: 326px; 
            padding-bottom: 65px;
        }

        .login-card .form-control {
            border: 1px solid #d5dae2;
            padding: 15px 25px;
            margin-bottom: 20px;
            min-height: 45px;
            font-size: 13px;
            line-height: 15;
            font-weight: normal; 
        }

        .login-card .form-control::-webkit-input-placeholder {
            color: #919aa3; 
        }

        .login-card .form-control::-moz-placeholder {
            color: #919aa3; 
        }

        .login-card .form-control:-ms-input-placeholder {
            color: #919aa3; 
        }

        .login-card .form-control::-ms-input-placeholder {
            color: #919aa3; 
        }

        .login-card .form-control::placeholder {
            color: #919aa3; 
        }

        .login-card .login-btn {
            padding: 13px 20px 12px;
            background-color: #DEAA5E;
            border-radius: 4px;
            font-size: 17px;
            font-weight: bold;
            line-height: 20px;
            color: #fff;
            margin-bottom: 24px; 
        }
        
        .login-card .login-btn:hover {
            border: 1px solid #A68048;
            background-color: #A68048;
            color: #ffffff; 
        }

        .login-card .forgot-password-link {
            font-size: 14px;
            color: #919aa3;
            margin-bottom: 12px; 
        }

        .login-card-footer-text {
            font-size: 16px;
            color: #0d2366;
            margin-bottom: 60px; 
        }

        @media (max-width: 767px) {
            .login-card-footer-text {
                margin-bottom: 24px; 
            } 
        }

        .login-card-footer-nav a {
            font-size: 14px;
            color: #919aa3; 
        }

        .alert-danger {
            /* background-color: red !important; */
            /* color: #ffffff !important; */
        }
    </style>
</head>
<body>
    <main class="d-flex align-items-center min-vh-100 py-3 py-md-0">
        <img src="{{ asset('/assets/images/bg.jpg') }}" class="background-img">
        <div class="container">
            {{-- @include('sms') --}}
            <div class="alert alert-danger">
                You do not have Username!
                {{-- {{ session()->get('message') }} --}}
            </div>
            <div class="card login-card">
                <div class="row no-gutters">
                <div class="col-md-5">
                    <img src="{{ asset('/assets/images/bg.jpg') }}" alt="login" class="login-card-img" sizes="20px">
                </div>
                <div class="col-md-7">
                    <div class="card-body">
                    <div class="brand-wrapper">
                        <a href="">
                            <img src="{{ asset('/assets/images/logo.png') }}" alt="logo" class="logo">
                        </a>
                    </div>
                    <p class="login-card-description">TF Motors | Admin Login</p>
                        <form method="POST" action="">
                            {{-- @csrf --}}
                            <div class="form-group">
                                <label for="email" class="sr-only">Email</label>
                                <input type="text" name="username" class="form-control" placeholder="Username">
                            </div>
                            <div class="form-group mb-4">
                                <label for="password" class="sr-only">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="***********">
                            </div>
                            <button class="btn btn-block login-btn mb-4" type="submit">Login</button>
                        </form>
                        <nav class="login-card-footer-nav">
                            &copy; <a href="">TF Motors</a> -  rights reserved 
                        </nav>
                        <a href="#" class="forgot-password-link">Version 2.0.0 | 2024</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>