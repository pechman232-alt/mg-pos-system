<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet"> --}}

    {{-- <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet"> --}}
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@100..900&family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/assets/css/login_style.min.css') }}">

    <link rel="shortcut icon" href="{{ asset('/assets/images/logo.png') }}">

    <title>LoginPage</title>
    <style>
     /* Body & background */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f5f5f5;
    margin: 0;
    padding: 0;
}

.background-img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.2; /* subtle background */
    z-index: -1;
}

/* Login container */
.login-card {
    max-width: 900px;
    margin: auto;
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

/* Left image */
.login-card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Right form area */
.card-body {
    padding: 40px;
}

.brand-wrapper {
    text-align: center;
    margin-bottom: 20px;
}

.brand-wrapper .logo {
    height: 50px;
    width: auto;
}

/* Form styling */
.login-card-description {
    text-align: center;
    font-weight: 600;
    font-size: 18px;
    margin-bottom: 30px;
    color: #333;
}

.form-control {
    border-radius: 8px;
    padding: 12px 15px;
    font-size: 16px;
    border: 1px solid #ddd;
    margin-bottom: 15px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #DEAA5E;
    box-shadow: 0 0 0 0.2rem rgba(222, 170, 94, 0.25);
}

/* Button */
.login-btn {
    background-color: #DEAA5E;
    color: #fff;
    padding: 12px;
    font-size: 16px;
    font-weight: bold;
    border: none;
    border-radius: 8px;
    transition: background 0.3s ease;
}

.login-btn:hover {
    background-color: #c99850;
}

/* Footer & links */
.login-card-footer-nav {
    text-align: center;
    font-size: 14px;
    color: #888;
    margin-top: 20px;
}

.login-card-footer-nav a {
    text-decoration: none;
    color: #DEAA5E;
}

.forgot-password-link {
    display: block;
    text-align: center;
    margin-top: 10px;
    font-size: 14px;
    color: #aaa;
    text-decoration: none;
}

.forgot-password-link:hover {
    color: #DEAA5E;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .row.no-gutters {
        flex-direction: column;
    }
    .login-card-img {
        height: 200px;
    }
    .card-body {
        padding: 20px;
    }
}

    </style>
</head>
<body>
    <main class="d-flex align-items-center min-vh-100 py-3 py-md-0">
        <img src="{{ asset('/assets/images/mg_7.jpgg') }}" class="background-img">
        <div class="container">

            @include('Helps.toast')

            <div class="card login-card">
                <div class="row no-gutters">
                <div class="col-md-5">
                    <img src="{{ asset('/assets/images/mg_7.jpg') }}" alt="login" class="login-card-img" sizes="20px">
                </div>
                <div class="col-md-7">
                    <div class="card-body">
                    <div class="brand-wrapper">
                        <a href="">
                            <img src="{{ asset('/assets/images/logo.png') }}" alt="logo" class="logo">
                        </a>
                    </div>

                    <p class="login-card-description">TF Motors | Admin Login</p>
                        <form method="POST" action="{{ url('login') }}" enctype="multipart/form-data">
                            @csrf
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
