<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STUXZ | AFK Locked</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            margin: 0;
            height: 100vh;
            background: url('https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=2070&auto=format&fit=crop') no-repeat center center fixed;
            background-size: cover;
            overflow: hidden;
            font-family: 'Segoe UI', sans-serif;
        }

        .afk-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(40px) grayscale(50%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        }

        .afk-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 40px;
            padding: 4rem;
            text-align: center;
            color: white;
            box-shadow: 0 50px 100px rgba(0,0,0,0.5);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        .pin-display {
            font-size: 2rem;
            letter-spacing: 15px;
            color: #3b82f6;
            margin: 2rem 0;
            background: rgba(0,0,0,0.3);
            padding: 15px;
            border-radius: 15px;
        }

        .gui-numpad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            max-width: 250px;
            margin: 0 auto;
        }

        .num-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
        }

        .num-btn:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: scale(1.1);
        }

        .num-btn:active {
            background: #3b82f6;
        }
    </style>
</head>
<body>

    <div class="afk-overlay">
        <div class="afk-card">
            <i data-lucide="shield-alert" class="text-primary mb-4" style="width: 80px; height: 80px;"></i>
            <h1 class="fw-bold mb-1">STATION FROZEN</h1>
            <p class="text-secondary mb-4">Enter your 4-digit PIN to resume gaming.</p>
            
            <div id="pin-view" class="pin-display">****</div>

            <div class="gui-numpad">
                <?php for($i=1; $i<=9; $i++): ?>
                    <div class="num-btn" onclick="pressNum(<?php echo $i; ?>)"><?php echo $i; ?></div>
                <?php endfor; ?>
                <div class="num-btn" onclick="clearPin()"><i data-lucide="x" style="width: 20px;"></i></div>
                <div class="num-btn" onclick="pressNum(0)">0</div>
                <div class="num-btn" onclick="checkPin()"><i data-lucide="check" style="width: 24px;"></i></div>
            </div>

            <div class="mt-5 pt-3 border-top border-secondary border-opacity-10">
                <small class="text-secondary d-block mb-3">Forgot your PIN? Call the administrator.</small>
                <button class="btn btn-outline-light btn-sm rounded-pill px-4" onclick="window.location.href='lock_screen.php'">LOGOUT SESSION</button>
            </div>
        </div>
    </div>

    <script src="../assets/js/client-timer.js"></script>
    <script>
        lucide.createIcons();
        let currentPin = "";
        
        function pressNum(n) {
            if(currentPin.length < 4) {
                currentPin += n;
                updateDisplay();
            }
        }

        function clearPin() {
            currentPin = "";
            updateDisplay();
        }

        function updateDisplay() {
            document.getElementById('pin-view').innerText = "*".repeat(currentPin.length) || "****";
        }

        function checkPin() {
            if(currentPin === "1234") {
                window.location.href = 'lock_screen.php';
            } else {
                alert('Wrong PIN, please try again.');
                clearPin();
            }
        }
    </script>
</body>
</html>
