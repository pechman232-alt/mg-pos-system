<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TF Motors | Operator Login</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons & Tailwind -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            margin: 0;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
        }

        .font-display { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Left Side Background */
        .bg-brand {
            background-color: #0F172A;
            background-image: url("{{ asset('/assets/images/mg_7.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }

        .bg-brand::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.8) 0%, rgba(208, 38, 44, 0.4) 100%);
        }

        /* Smooth Input Fields */
        .input-field {
            width: 100%;
            background-color: #f1f5f9;
            border: 2px solid transparent;
            color: #1e293b;
            padding: 1rem 1.25rem 1rem 3.25rem;
            border-radius: 0.75rem;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .input-field:focus {
            background-color: #ffffff;
            border-color: #4F46E5; /* Indigo 600 */
            outline: none;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.25rem;
            transition: color 0.3s ease;
        }

        .group:focus-within .input-icon {
            color: #4F46E5;
        }

        /* Submit Button */
        .btn-submit {
            background-color: #0F172A;
            color: #ffffff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-submit:hover {
            background-color: #1e293b;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.2);
        }

        /* Loading Spinner */
        .spinner {
            display: none;
            width: 20px; height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #ffffff;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* Smooth Entry Animation */
        .fade-in-up {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
        }

        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
    </style>
</head>
<body class="h-screen w-full flex">

    <!-- Left Side: Image Banner -->
    <div class="hidden lg:flex lg:w-1/2 bg-brand relative flex-col justify-between p-12">
        <div class="relative z-10">
            <img src="{{ asset('/assets/images/logo.png') }}" class="h-12 drop-shadow-md" alt="TF Logo">
        </div>
        
        <div class="relative z-10 text-white pb-8">
            <h1 class="font-display text-4xl font-extrabold tracking-tight mb-4 leading-tight">
                Streamline Your<br>Aftersales Operations.
            </h1>
            <p class="text-slate-300 text-lg font-medium max-w-md">
                TF Motors Operator Portal provides real-time insights, service capacity management, and performance tracking.
            </p>
        </div>
    </div>

    <!-- Right Side: Clean Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white relative z-10">
        
        <div class="w-full max-w-md">
            
            <!-- Mobile Logo (Hidden on Desktop) -->
            <div class="text-center lg:hidden mb-8 fade-in-up">
                <img src="{{ asset('/assets/images/logo.png') }}" class="h-12 mx-auto" alt="TF Logo">
            </div>

            <!-- Form Header -->
            <div class="mb-10 text-center lg:text-left fade-in-up delay-100">
                <h2 class="font-display text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Welcome Back</h2>
                <p class="text-slate-500 text-sm font-medium">Please sign in to access your operator dashboard.</p>
            </div>

            <!-- The Form -->
            <form id="loginForm" action="{{ route('login') }}" method="POST" class="space-y-5 fade-in-up delay-200">
                @csrf
                
                <!-- Username -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-2">Username / Operator ID</label>
                    <div class="relative group">
                        <i class="bx bx-id-card input-icon"></i>
                        <input type="text" id="username" name="username" placeholder="Enter your username" required autofocus autocomplete="username" class="input-field">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Password</label>
                        <a href="#" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors">Forgot Password?</a>
                    </div>
                    <div class="relative group">
                        <i class="bx bx-lock-alt input-icon"></i>
                        <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password" class="input-field" style="padding-right: 3rem;">
                        <i id="pwToggle" role="button" class="bx bx-show absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 cursor-pointer text-xl transition-colors"></i>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center mt-2">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">Keep me signed in</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 fade-in-up delay-300">
                    <button type="submit" id="submitBtn" class="btn-submit w-full py-3.5 rounded-xl font-bold text-[15px] flex items-center justify-center gap-3">
                        <span id="btnText">Sign In to Dashboard</span>
                        <div id="spinner" class="spinner"></div>
                    </button>
                </div>
            </form>
            
            <!-- Footer -->
            <div class="mt-12 text-center fade-in-up delay-300">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">© {{ date('Y') }} TF Motors Cambodia. All rights reserved.</p>
            </div>
        </div>
    </div>

    <script>
        // Password Visibility Toggle (Smooth & Simple)
        const pwInput = document.getElementById('password');
        const pwToggle = document.getElementById('pwToggle');
        
        pwToggle.addEventListener('click', () => {
            const isPassword = pwInput.type === 'password';
            pwInput.type = isPassword ? 'text' : 'password';
            pwToggle.className = isPassword 
                ? 'bx bx-hide absolute right-4 top-1/2 -translate-y-1/2 text-slate-700 cursor-pointer text-xl transition-colors' 
                : 'bx bx-show absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 cursor-pointer text-xl transition-colors';
        });

        // Form Submit Loading State
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const spinner = document.getElementById('spinner');
            
            // Prevent double click
            btn.style.pointerEvents = 'none';
            btn.style.opacity = '0.9';
            
            // Show Loading
            btnText.textContent = 'Authenticating...';
            spinner.style.display = 'block';
        });
    </script>
</body>
</html>