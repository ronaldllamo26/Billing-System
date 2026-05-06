<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Login | STUXZ Core</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="assets/js/lucide.min.js"></script>
    <style>
        :root {
            --glass-bg: rgba(20, 20, 25, 0.4); 
            --glass-border: rgba(255, 255, 255, 0.2);
            --accent-blue: #3b82f6;
            --accent-red: #ef4444;
        }

        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', system-ui, sans-serif;
            overflow: hidden;
            background: #000;
        }

        /* Slideshow */
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
            filter: brightness(0.5) saturate(1.1);
        }

        .slide-img.active { opacity: 1; }

        .bg-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: radial-gradient(circle at center, transparent 0%, rgba(0, 0, 0, 0.7) 100%);
            z-index: 2;
            pointer-events: none;
        }

        /* DAMBUHALA PANTAY PC NUMBER */
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
            font-size: 120px; /* Pantay na laki */
            font-weight: 900;
            line-height: 1;
        }

        .pc-badge-pantay .label {
            color: var(--accent-blue);
        }

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
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 40px;
            width: 380px;
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.7);
            text-align: center;
        }

        .form-control {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid var(--glass-border);
            color: #fff;
            padding: 14px;
            border-radius: 12px;
            margin-bottom: 15px;
        }

        .btn-nexus {
            background: var(--accent-blue);
            border: none;
            color: #fff;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            padding: 16px;
            border-radius: 12px;
            width: 100%;
        }

        .integrated-controls {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            display: flex;
            justify-content: center;
            gap: 40px;
        }

        .control-item {
            color: rgba(255,255,255,0.7);
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            font-size: 10px;
            font-weight: 800;
        }

        .control-item:hover { color: #fff; }
        .control-item.power:hover { color: var(--accent-red); }

        #confirm-modal {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.85);
            backdrop-filter: blur(8px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .modal-card {
            background: rgba(15, 23, 42, 0.95);
            border: 1px solid var(--accent-blue);
            padding: 40px;
            border-radius: 24px;
            width: 320px;
            text-align: center;
        }

        .modal-btn {
            border: none;
            padding: 12px;
            border-radius: 12px;
            font-weight: 800;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    
    <div id="slideshow-container">
        <img src="valorant.png" class="slide-img active">
        <img src="lol.png" class="slide-img">
        <img src="dota.png" class="slide-img">
        <img src="cyberpunk.png" class="slide-img">
    </div>
    
    <div class="bg-overlay"></div>

    <!-- PANTAY NA DAMBUHALA PC NUMBER -->
    <div class="pc-badge-pantay">
        <div class="label">PC</div>
        <div class="number">1</div>
    </div>

    <div class="content-layer">
        <div class="login-box">
            <h3 class="fw-black text-white mb-1" style="text-shadow: 0 2px 4px rgba(0,0,0,0.5);">STUXZ <span class="text-info">NEXUS</span></h3>
            <p class="text-white-50 small mb-4" style="font-size: 11px;">CYBERCAFE MANAGEMENT CORE</p>
            
            <form action="actions/login.php" method="POST">
                <input type="text" name="username" class="form-control" placeholder="USERNAME" required>
                <input type="password" name="password" class="form-control" placeholder="SECURITY KEY" required>
                <button type="submit" class="btn btn-nexus">INITIALIZE SYSTEM</button>
                <a href="client/lock_screen.php" class="text-white-50 small text-decoration-none d-block mt-3" style="font-size: 11px;">SWITCH TO CLIENT INTERFACE</a>
            </form>

            <div class="integrated-controls">
                <div class="control-item reload" onclick="showConfirm('RESTART', 'restart')">
                    <i data-lucide="refresh-cw" style="width: 20px;"></i>
                    <span>RESTART</span>
                </div>
                <div class="control-item power" onclick="showConfirm('SHUTDOWN', 'shutdown')">
                    <i data-lucide="power" style="width: 20px;"></i>
                    <span>SHUTDOWN</span>
                </div>
            </div>
        </div>
    </div>

    <div id="confirm-modal">
        <div class="modal-card">
            <h4 class="text-white mb-2" id="modal-title">CONFIRM</h4>
            <p class="text-white-50 small mb-4">Execute system command for PC 1?</p>
            <button class="modal-btn" style="background: var(--accent-red); color: #fff;" onclick="executeAction()">YES, EXECUTE</button>
            <button class="modal-btn" style="background: rgba(255,255,255,0.1); color: #fff;" onclick="hideConfirm()">CANCEL</button>
        </div>
    </div>

    <script>
        if (typeof lucide !== 'undefined') lucide.createIcons();

        const slides = document.querySelectorAll('.slide-img');
        let currentSlide = 0;
        setInterval(() => {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }, 8000);

        let pendingAction = '';
        function showConfirm(title, action) {
            document.getElementById('modal-title').innerText = title;
            document.getElementById('confirm-modal').style.display = 'flex';
            pendingAction = action;
        }

        function hideConfirm() {
            document.getElementById('confirm-modal').style.display = 'none';
        }

        function executeAction() {
            if(pendingAction === 'restart') location.reload();
            else alert('System Shutdown initiated...');
            hideConfirm();
        }
    </script>
</body>
</html>
