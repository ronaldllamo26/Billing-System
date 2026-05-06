<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Lock | STATION FROZEN</title>
    <!-- Local Assets -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="../assets/js/lucide.min.js"></script>
    <style>
        :root {
            --glass-bg: rgba(10, 15, 30, 0.85);
            --glass-border: rgba(255, 255, 255, 0.1);
            --accent-blue: #3b82f6;
            --accent-red: #ef4444;
            --accent-green: #22c55e;
        }

        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', system-ui, sans-serif;
            overflow: hidden;
            background: #020617 !important;
        }

        /* Slideshow - Identical to index.php */
        #slideshow-container {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 1;
        }

        .slide-img {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            object-fit: cover;
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
            filter: brightness(0.4) saturate(1.2);
        }

        .slide-img.active { opacity: 1; }

        .bg-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: radial-gradient(circle at center, transparent 0%, rgba(2, 6, 23, 0.8) 100%);
            z-index: 2;
            pointer-events: none;
        }

        /* GIANT PC NUMBER - Identical to index.php */
        .pc-badge-pantay {
            position: fixed;
            top: 40px;
            left: 50px;
            z-index: 100;
            display: flex;
            align-items: center;
            gap: 20px;
            font-family: 'Arial Black', sans-serif;
            color: #fff;
            text-shadow: 0 0 30px rgba(59, 130, 246, 0.6), 0 0 60px rgba(59, 130, 246, 0.3);
            letter-spacing: -5px;
        }

        .pc-badge-pantay .label, 
        .pc-badge-pantay .number {
            font-size: 120px;
            font-weight: 900;
            line-height: 1;
        }

        .pc-badge-pantay .label { color: var(--accent-blue); }

        /* Lock Box Container */
        .content-layer {
            position: relative;
            z-index: 10;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 30px;
            padding: 40px;
            width: 380px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8);
            text-align: center;
        }

        .pin-display {
            font-size: 2.5rem;
            letter-spacing: 15px;
            color: var(--accent-blue);
            margin: 1.5rem 0;
            background: rgba(0,0,0,0.4);
            padding: 15px;
            border-radius: 15px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Consolas', monospace;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .gui-numpad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            max-width: 280px;
            margin: 0 auto;
        }

        .num-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            width: 75px; height: 75px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 900;
            cursor: pointer;
            transition: 0.1s;
        }

        .num-btn:hover { border-color: var(--accent-blue); background: rgba(59, 130, 246, 0.1); }
        .num-btn:active { background: var(--accent-blue); transform: scale(0.9); }

        .status-msg {
            font-size: 12px;
            font-weight: bold;
            margin-top: 15px;
            min-height: 18px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-15px); }
            75% { transform: translateX(15px); }
        }

        .error { animation: shake 0.2s ease-in-out 3; border-color: var(--accent-red) !important; color: var(--accent-red) !important; }
        .success { border-color: var(--accent-green) !important; color: var(--accent-green) !important; }
    </style>
</head>
<body>
    
    <!-- Identical Background to index.php -->
    <div id="slideshow-container">
        <img src="../valorant.png" class="slide-img active">
        <img src="../lol.png" class="slide-img">
        <img src="../dota.png" class="slide-img">
        <img src="../cyberpunk.png" class="slide-img">
    </div>
    
    <div class="bg-overlay"></div>

    <!-- Identical PC Badge to index.php -->
    <div class="pc-badge-pantay">
        <div class="label">PC</div>
        <div class="number">1</div>
    </div>

    <div class="content-layer">
        <div class="login-box" id="lock-card">
            <h3 class="fw-black text-white mb-1">STATION <span class="text-info">FROZEN</span></h3>
            <p class="text-white-50 small mb-4" style="font-size: 11px;">ENTER PIN TO RESUME SESSION</p>
            
            <div id="pin-view" class="pin-display">····</div>

            <div class="gui-numpad">
                <div class="num-btn" onclick="pressNum(1)">1</div>
                <div class="num-btn" onclick="pressNum(2)">2</div>
                <div class="num-btn" onclick="pressNum(3)">3</div>
                <div class="num-btn" onclick="pressNum(4)">4</div>
                <div class="num-btn" onclick="pressNum(5)">5</div>
                <div class="num-btn" onclick="pressNum(6)">6</div>
                <div class="num-btn" onclick="pressNum(7)">7</div>
                <div class="num-btn" onclick="pressNum(8)">8</div>
                <div class="num-btn" onclick="pressNum(9)">9</div>
                <div class="num-btn" style="color: var(--accent-red);" onclick="clearPin()"><i data-lucide="delete"></i></div>
                <div class="num-btn" onclick="pressNum(0)">0</div>
                <div class="num-btn" style="color: var(--accent-green);" onclick="checkPin()"><i data-lucide="unlock"></i></div>
            </div>

            <div class="status-msg" id="status-msg"></div>

            <div class="mt-4 pt-4 border-top border-white border-opacity-10">
                <button class="btn btn-outline-danger btn-sm rounded-pill px-4" onclick="confirmLogout()">LOGOUT SESSION</button>
            </div>
        </div>
    </div>

    <script>
        if (typeof lucide !== 'undefined') lucide.createIcons();

        // Slideshow logic identical to index.php
        const slides = document.querySelectorAll('.slide-img');
        let currentSlide = 0;
        setInterval(() => {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }, 8000);

        // PIN Logic
        let currentPin = "";
        const PIN_VIEW = document.getElementById('pin-view');
        const CARD = document.getElementById('lock-card');
        const MSG = document.getElementById('status-msg');
        
        function pressNum(n) {
            if(currentPin.length < 4) {
                currentPin += n;
                updateDisplay();
                if (currentPin.length === 4) checkPin();
            }
        }

        function clearPin() {
            currentPin = "";
            updateDisplay();
            MSG.innerText = "";
            PIN_VIEW.classList.remove('error', 'success');
        }

        function updateDisplay() {
            let dots = "····";
            let display = "*".repeat(currentPin.length) + dots.substring(currentPin.length);
            PIN_VIEW.innerText = display;
        }

        function checkPin() {
            if(currentPin === "1234") {
                PIN_VIEW.classList.add('success');
                MSG.innerText = "ACCESS GRANTED - UNLOCKING...";
                MSG.style.color = "var(--accent-green)";
                setTimeout(() => { window.location.href = 'lock_screen.php'; }, 500);
            } else {
                PIN_VIEW.classList.add('error');
                MSG.innerText = "INVALID PIN - TRY AGAIN";
                MSG.style.color = "var(--accent-red)";
                setTimeout(() => { clearPin(); }, 800);
            }
        }

        function confirmLogout() {
            if(confirm("Terminate session?")) window.location.href = '../index.php';
        }
    </script>
</body>
</html>
