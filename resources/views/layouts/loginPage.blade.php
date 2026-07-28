<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.tailwindcss.com"></script>

    <title>TF Motors | MG Cambodia — Operator Access</title>

    <style>
        :root {
            --midnight: #04060f;
            --circuit: #4338ca;
            --violet: #7c3aed;
            --ignition-red: #e11d48;
            --dash-amber: #fbbf24;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--midnight);
            overflow: hidden;
            margin: 0;
            -webkit-font-smoothing: antialiased;
        }

        .font-display { font-family: 'Sora', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        .bg-portal {
            position: fixed; inset: 0;
            background: linear-gradient(rgba(4, 6, 15, 0.88), rgba(4, 6, 15, 0.78)),
                        url("{{ asset('/assets/images/mg_7.jpg') }}") center/cover no-repeat;
            z-index: -2;
        }

        /* BESPOKE AMBIENT BACKGROUND — light streaks instead of generic particles */
        #ambient { position: fixed; inset: 0; z-index: -1; pointer-events: none; overflow: hidden; }
        .streak {
            position: absolute;
            height: 1px;
            width: 160px;
            left: -200px;
            background: linear-gradient(90deg, transparent, rgba(165,180,252,0.55), transparent);
            animation: streak-move linear infinite;
        }
        @keyframes streak-move {
            to { transform: translateX(160vw); }
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
            background: linear-gradient(135deg, var(--circuit), var(--violet), var(--ignition-red));
            background-size: 200% auto;
            transition: all 0.3s ease;
        }
        .btn-ignition:hover { background-position: right center; }
        .btn-ignition:focus-visible {
            outline: 2px solid #a5b4fc;
            outline-offset: 3px;
        }

        input:focus-visible {
            outline: 2px solid rgba(165, 180, 252, 0.6);
            outline-offset: 2px;
        }

        /* DASH READOUT BAR */
        .dash-readout {
            display: flex; align-items: center; justify-content: space-between;
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px; letter-spacing: 0.12em;
            color: rgba(165, 180, 252, 0.55);
            text-transform: uppercase;
        }
        .status-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: #34d399;
            box-shadow: 0 0 8px #34d399;
            animation: pulse-dot 2s ease-in-out infinite;
        }
        @keyframes pulse-dot { 0%, 100% { opacity: 1; } 50% { opacity: 0.35; } }

        /* AUTH GAUGE */
        #needleGroup { transform-origin: 100px 100px; transform: rotate(-90deg); transition: transform 0.5s cubic-bezier(.34,1.56,.64,1); }
        #gaugeFill { transition: stroke-dashoffset 0.5s cubic-bezier(.34,1.56,.64,1); }
        .gauge-redline #needleGroup { filter: drop-shadow(0 0 6px var(--ignition-red)); }

        /* TERMINAL STYLING */
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

        /* PASSWORD VISIBILITY TOGGLE */
        .pw-toggle { cursor: pointer; }
        .pw-toggle:focus-visible { outline: 2px solid rgba(165, 180, 252, 0.6); border-radius: 4px; }

        @media (prefers-reduced-motion: reduce) {
            .streak { display: none; }
            .drive-out-sequence { animation: none !important; opacity: 0; }
            #needleGroup, #gaugeFill { transition: none !important; }
            .status-dot { animation: none; }
        }
    </style>
</head>
<body class="h-screen w-full flex items-center justify-center p-4">

    <div id="ambient"></div>

    <div id="preloader">
        <img src="{{ asset('/assets/images/logo.png') }}" class="h-16 animate-pulse" alt="Logo">
        <div class="mt-4 text-indigo-400 font-bold tracking-[8px] text-[9px] uppercase opacity-60 font-mono">Initializing...</div>
    </div>

    <div class="bg-portal"></div>

    <main id="login-wrapper" class="w-full max-w-[420px] opacity-0 transition-all duration-700 translate-y-4">
        <div class="dash-readout mb-3 px-2">
            <span class="flex items-center gap-2"><span class="status-dot"></span>Systems Nominal</span>
            <span id="real-time" class="font-mono">--:--</span>
        </div>

        <div class="glass-panel rounded-[40px] p-10 text-center" id="loginCard">
            <div class="mb-6">
                <img src="{{ asset('/assets/images/logo.png') }}" class="h-12 mx-auto mb-6" alt="TF Logo">
                <h1 class="font-display text-3xl font-extrabold text-white italic tracking-tight">TF MOTORS</h1>
                <p class="text-indigo-300/40 font-bold text-[10px] uppercase tracking-widest mt-2 font-mono">MG Cambodia · Operator Access</p>
            </div>

            <!-- AUTH GAUGE -->
            <div class="flex flex-col items-center -mt-2 mb-4" id="gaugeWrap">
                <svg viewBox="0 0 200 110" class="w-40 h-auto">
                    <defs>
                        <linearGradient id="gaugeGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#4338ca"/>
                            <stop offset="55%" stop-color="#7c3aed"/>
                            <stop offset="100%" stop-color="#e11d48"/>
                        </linearGradient>
                    </defs>
                    <path d="M10,100 A90,90 0 0,1 190,100" fill="none" stroke="rgba(255,255,255,0.08)" stroke-width="10" stroke-linecap="round"/>
                    <path id="gaugeFill" d="M10,100 A90,90 0 0,1 190,100" fill="none" stroke="url(#gaugeGrad)" stroke-width="10" stroke-linecap="round" stroke-dasharray="282.74" stroke-dashoffset="282.74"/>
                    <!-- ticks -->
                    <line x1="2" y1="100" x2="22" y2="100" stroke="rgba(255,255,255,0.25)" stroke-width="2"/>
                    <line x1="30.7" y1="30.7" x2="44.8" y2="44.8" stroke="rgba(255,255,255,0.25)" stroke-width="2"/>
                    <line x1="100" y1="2" x2="100" y2="22" stroke="rgba(255,255,255,0.25)" stroke-width="2"/>
                    <line x1="169.3" y1="30.7" x2="155.2" y2="44.8" stroke="rgba(255,255,255,0.25)" stroke-width="2"/>
                    <line x1="198" y1="100" x2="178" y2="100" stroke="rgba(255,255,255,0.25)" stroke-width="2"/>
                    <g id="needleGroup">
                        <line x1="100" y1="100" x2="100" y2="22" stroke="#f8fafc" stroke-width="3" stroke-linecap="round"/>
                    </g>
                    <circle cx="100" cy="100" r="6" fill="#f8fafc"/>
                </svg>
                <div class="font-mono text-[9px] uppercase tracking-[0.2em] text-indigo-300/50 -mt-1" id="gaugeState">Standby</div>
            </div>

            <form id="loginForm" action="{{ url('login') }}" method="POST" class="space-y-4">
                @csrf
                <div class="relative group">
                    <i class="bx bx-id-card absolute left-5 top-1/2 -translate-y-1/2 text-slate-500"></i>
                    <input type="text" id="username" name="username" placeholder="User Name" required autofocus autocomplete="username"
                        class="w-full pl-12 pr-6 py-4 rounded-xl bg-white/5 border border-white/10 text-white outline-none focus:border-indigo-500/50 transition-all text-sm">
                </div>
                <div class="relative group">
                    <i class="bx bx-lock-open-alt absolute left-5 top-1/2 -translate-y-1/2 text-slate-500"></i>
                    <input type="password" id="password" name="password" placeholder="Password" required autocomplete="current-password"
                        class="w-full pl-12 pr-12 py-4 rounded-xl bg-white/5 border border-white/10 text-white outline-none focus:border-indigo-500/50 transition-all text-sm">
                    <i id="pwToggle" tabindex="0" role="button" aria-label="Show password" class="bx bx-show pw-toggle absolute right-5 top-1/2 -translate-y-1/2 text-slate-500"></i>
                </div>

                <div class="flex items-center justify-between px-1 text-[11px] text-slate-400">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="rounded bg-white/5 border-white/20 accent-indigo-500">
                        Remember me
                    </label>
                    <a href="#" class="text-indigo-300/60 hover:text-indigo-300 transition-colors">Forgot access?</a>
                </div>

                <div class="pt-2">
                    <button type="submit" id="submitBtn" class="btn-ignition w-full py-4 rounded-xl text-white font-black uppercase tracking-widest text-[11px] flex items-center justify-center gap-3">
                        <span id="btnLabel">Start Ignition</span>
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
        // Clock — fires immediately, then keeps a light 30s refresh since only HH:MM is shown
        const updateClock = () => {
            document.getElementById('real-time').textContent = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
        };
        updateClock();
        setInterval(updateClock, 30000);

        // Entry sequence
        window.addEventListener('load', () => {
            setTimeout(() => {
                document.getElementById('preloader').style.opacity = '0';
                document.getElementById('login-wrapper').classList.remove('opacity-0', 'translate-y-4');
                setTimeout(() => document.getElementById('preloader').remove(), 500);
            }, 500);
        });

        // --- AUTH GAUGE ---
        const ARC_LENGTH = 282.74;
        const needleGroup = document.getElementById('needleGroup');
        const gaugeFill = document.getElementById('gaugeFill');
        const gaugeState = document.getElementById('gaugeState');
        const gaugeWrap = document.getElementById('gaugeWrap');

        function setGauge(value, instant) {
            value = Math.max(0, Math.min(100, value));
            const deg = -90 + (value / 100) * 180;
            needleGroup.style.transition = instant ? 'none' : 'transform 0.5s cubic-bezier(.34,1.56,.64,1)';
            needleGroup.style.transform = `rotate(${deg}deg)`;
            gaugeFill.style.transition = instant ? 'none' : 'stroke-dashoffset 0.5s cubic-bezier(.34,1.56,.64,1)';
            gaugeFill.style.strokeDashoffset = ARC_LENGTH * (1 - value / 100);
            gaugeState.textContent = value < 10 ? 'Standby' : value < 90 ? 'Verifying' : 'Engaged';
        }

        function recalcGauge() {
            const u = document.getElementById('username').value.length > 0;
            const p = document.getElementById('password').value;
            let v = 0;
            if (u) v += 35;
            v += Math.min(p.length * 8, 65);
            setGauge(v);
        }
        document.getElementById('username').addEventListener('input', recalcGauge);
        document.getElementById('password').addEventListener('input', recalcGauge);

        function revEngine(callback) {
            let ticks = 0;
            const jitter = setInterval(() => {
                setGauge(82 + Math.random() * 18, true);
                ticks++;
                if (ticks > 6) {
                    clearInterval(jitter);
                    setGauge(100);
                    gaugeWrap.classList.add('gauge-redline');
                    callback && callback();
                }
            }, 90);
        }

        // Password visibility toggle
        const pwInput = document.getElementById('password');
        const pwToggle = document.getElementById('pwToggle');
        function togglePw() {
            const show = pwInput.type === 'password';
            pwInput.type = show ? 'text' : 'password';
            pwToggle.className = show ? 'bx bx-hide pw-toggle absolute right-5 top-1/2 -translate-y-1/2 text-slate-500' : 'bx bx-show pw-toggle absolute right-5 top-1/2 -translate-y-1/2 text-slate-500';
            pwToggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        }
        pwToggle.addEventListener('click', togglePw);
        pwToggle.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); togglePw(); } });

        // Terminal boot log
        const logs = [
            "> Reading key fob...",
            "> Verifying chassis ID...",
            "> ECU handshake OK.",
            "> Immobilizer released.",
            "> Diagnostics: 0 faults.",
            "> Welcome aboard, Operator."
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
                    if (i === logs.length - 1) setTimeout(callback, 200);
                }, i * 120);
            });
        }

        // Submit sequence
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            const nitro = document.getElementById('nitro-loader');

            btn.style.pointerEvents = 'none';
            btn.style.transform = 'scale(0.98)';
            document.getElementById('btnLabel').innerText = 'IGNITING...';
            nitro.classList.remove('hidden');

            let done = 0;
            const finishCheck = () => { done++; if (done === 2) finish(); };
            const finish = () => {
                document.getElementById('loginCard').classList.add('drive-out-sequence');
                setTimeout(() => this.submit(), 400);
            };

            revEngine(finishCheck);
            runTerminal(finishCheck);
        });

        // Ambient light streaks — bespoke, no external particle library
        (function buildStreaks() {
            const ambient = document.getElementById('ambient');
            const rows = [12, 24, 38, 52, 68, 82];
            rows.forEach((top, i) => {
                const el = document.createElement('div');
                el.className = 'streak';
                el.style.top = top + '%';
                el.style.animationDuration = (7 + i * 1.6) + 's';
                el.style.animationDelay = (i * 1.1) + 's';
                el.style.opacity = 0.25 + (i % 3) * 0.1;
                ambient.appendChild(el);
            });
        })();
    </script>
</body>
</html>