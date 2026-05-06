<?php
// Main Login Page Redirect or Landing
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STUXZ | Nexus Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/glassmorphism.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at center, #1a202c 0%, #0a0e17 100%);
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 2.5rem;
        }
    </style>
</head>
<body>
    <div class="login-card glass-card text-center">
        <h2 class="fw-bold mb-1" style="color: var(--accent-blue);">STUXZ<span style="color: #fff;">.AI</span></h2>
        <p class="text-secondary small mb-4">CYBERCAFE MANAGEMENT SYSTEM</p>
        
        <form action="admin/dashboard.php" method="POST">
            <div class="mb-3 text-start">
                <label class="form-label small text-secondary">Username</label>
                <input type="text" class="form-control bg-dark border-secondary border-opacity-25 text-white p-3 rounded-3" placeholder="Enter username" required>
            </div>
            <div class="mb-4 text-start">
                <label class="form-label small text-secondary">Security Key</label>
                <input type="password" class="form-control bg-dark border-secondary border-opacity-25 text-white p-3 rounded-3" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 p-3 fw-bold rounded-3" style="background: linear-gradient(45deg, #00d4ff, #00ff88); border: none;">
                INITIALIZE NEXUS
            </button>
        </form>
        
        <div class="mt-4 pt-3 border-top border-secondary border-opacity-10">
            <a href="client/lock_screen.php" class="text-decoration-none text-secondary small">
                Switch to Client Interface
            </a>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
