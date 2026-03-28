<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tsparticles/all@3.1.0/tsparticles.all.bundle.min.js"></script>

    <title>TF Motors | Center</title>
    
    <style>
        :root {
            --brand-primary: #6366f1;
            --brand-accent: #ec4899;
            --midnight: #020617;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--midnight);
            overflow: hidden;
            margin: 0;
            -webkit-font-smoothing: antialiased;
        }

        #tsparticles { position: fixed; inset: 0; z-index: 0; pointer-events: none; }

        .bg-portal {
            position: fixed; inset: 0;
            background: linear-gradient(rgba(2, 6, 23, 0.85), rgba(2, 6, 23, 0.7)), 
                        url("{{ asset('/assets/images/mg_7.jpg') }}") center/cover no-repeat;
            z-index: -1;
        }

        /* HARDWARE ACCELERATED DRIVE-OUT */
        .drive-out-sequence {
            will-change: transform, opacity, filter;
            animation: driveOut 0.6s cubic-bezier(0.4, 0, 1, 1) forwards;
        }

        @keyframes driveOut {
            0% { transform: translate3d(0, 0, 0) scale(1); filter: blur(0); }
            100% { transform: translate3d(2000px, 0, 0) scale(1.1); filter: blur(10px); opacity: 0; }
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .btn-ignition {
            background: linear-gradient(135deg, #4f46e5, #7c3aed, #ec4899);
            background-size: 200% auto;
            transition: all 0.3s ease;
        }

        /* TERMINAL STYLING - FAST EXPAND */
        #log-terminal {
            font-family: 'JetBrains Mono', monospace;
            background: rgba(0, 0, 0, 0.4);
            border-radius: 12px;
            height: 0;
            overflow: hidden;
            transition: height 0.2s ease-out;
        }
        #log-terminal.active { height: 100px; margin-top: 20px; border: 1px solid rgba(99, 102, 241, 0.2); padding: 10px; }
        
        .log-entry { 
            font-size: 10px; 
            color: #a5b4fc; 
            margin-bottom: 2px; 
            opacity: 0; 
            animation: logFade 0.1s forwards;
        }
        @keyframes logFade { to { opacity: 1; } }

        #preloader {
            position: fixed; inset: 0; background: var(--midnight);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            z-index: 100;
        }
    </style>
</head>
<body class="h-screen w-full flex items-center justify-center p-4">

    <div id="tsparticles"></div>

    <div id="preloader">
        <img src="{{ asset('/assets/images/logo.png') }}" class="h-16 animate-pulse" alt="Logo">
        <div class="mt-4 text-indigo-400 font-bold tracking-[8px] text-[9px] uppercase opacity-60">Initializing...</div>
    </div>

    <div class="bg-portal"></div>

    <main id="login-wrapper" class="w-full max-w-[420px] opacity-0 transition-all duration-700 translate-y-4">
        <div class="glass-panel rounded-[40px] p-10 text-center" id="loginCard">
            <div class="mb-10">
                <img src="{{ asset('/assets/images/logo.png') }}" class="h-12 mx-auto mb-6" alt="TF Logo">
                <h1 class="text-3xl font-black text-white italic tracking-tight">TF MOTORS</h1>
                <p class="text-indigo-300/40 font-bold text-[10px] uppercase tracking-widest mt-2">AI Command Center</p>
            </div>

            <form id="loginForm" action="{{ url('login') }}" method="POST" class="space-y-4">
                @csrf
                <div class="relative group">
                    <i class="bx bx-id-card absolute left-5 top-1/2 -translate-y-1/2 text-slate-500"></i>
                    <input type="text" name="username" placeholder="Operator Identity" required autofocus
                        class="w-full pl-12 pr-6 py-4 rounded-xl bg-white/5 border border-white/10 text-white outline-none focus:border-indigo-500/50 transition-all text-sm">
                </div>
                <div class="relative group">
                    <i class="bx bx-lock-open-alt absolute left-5 top-1/2 -translate-y-1/2 text-slate-500"></i>
                    <input type="password" name="password" placeholder="Access Cipher" required
                        class="w-full pl-12 pr-6 py-4 rounded-xl bg-white/5 border border-white/10 text-white outline-none focus:border-indigo-500/50 transition-all text-sm">
                </div>
                <div class="pt-4">
                    <button type="submit" id="submitBtn" class="btn-ignition w-full py-4 rounded-xl text-white font-black uppercase tracking-widest text-[11px] flex items-center justify-center gap-3">
                        <span id="btnLabel">Engage System</span>
                        <div id="nitro-loader" class="hidden w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
                    </button>
                </div>
            </form>

            <div id="log-terminal">
                <div id="log-container"></div>
            </div>
        </div>
    </main>

    <script>
        // Optimized Clock
        const updateClock = () => {
            document.getElementById('real-time').textContent = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
        };
        // Interval set to 30s to save resources since we only show HH:MM
        setInterval(updateClock, 30000); 

        // Fast Entry Sequence
        window.addEventListener('load', () => {
            setTimeout(() => {
                document.getElementById('preloader').style.opacity = '0';
                document.getElementById('login-wrapper').classList.remove('opacity-0', 'translate-y-4');
                setTimeout(() => document.getElementById('preloader').remove(), 500);
            }, 500); // Shorter preloader
        });

        // Fast Terminal Animation
        const logs = [
            "> Handshake...",
            "> Identity verified.",
            "> Layer 4 Encrypted.",
            "> Database Synced.",
            "> Neural Link active.",
            "> Welcome, Operator."
        ];

        function runTerminal(callback) {
            const terminal = document.getElementById('log-terminal');
            const container = document.getElementById('log-container');
            terminal.classList.add('active');
            
            logs.forEach((text, i) => {
                setTimeout(() => {
                    const div = document.createElement('div');
                    div.className = 'log-entry';
                    div.textContent = text;
                    container.appendChild(div);
                    // On last log, callback faster
                    if (i === logs.length - 1) setTimeout(callback, 200);
                }, i * 120); // Fast log speed (120ms)
            });
        }

        // Fast Form Submit
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            const nitro = document.getElementById('nitro-loader');
            
            btn.style.pointerEvents = 'none';
            btn.style.transform = 'scale(0.98)';
            document.getElementById('btnLabel').innerText = 'ENGAGING...';
            nitro.classList.remove('hidden');

            runTerminal(() => {
                document.getElementById('loginCard').classList.add('drive-out-sequence');
                setTimeout(() => this.submit(), 400); // Minimal delay before submit
            });
        });

        // Background Particles
        tsParticles.load("tsparticles", {
            particles: {
                number: { value: 40 },
                color: { value: "#6366f1" },
                opacity: { value: 0.2 },
                size: { value: 1.5 },
                move: { enable: true, speed: 0.5 }
            }
        });
    </script>
</body>
</html>