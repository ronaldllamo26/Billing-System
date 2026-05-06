<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STUXZ | Station Online</title>
    <!-- Local Assets -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="../assets/js/lucide.min.js"></script>
    <style>
        :root {
            --accent-blue: #3b82f6;
            --accent-green: #22c55e;
            --accent-red: #ef4444;
            --glass-bg: rgba(15, 23, 42, 0.85);
        }

        body {
            margin: 0;
            height: 100vh;
            background: #020617 url('../valorant.png') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', system-ui, sans-serif;
            overflow: hidden;
            color: white;
        }

        .bg-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: radial-gradient(circle at center, transparent 0%, rgba(2, 6, 23, 0.5) 100%);
            z-index: 1;
            pointer-events: none;
        }

        /* Desktop Icons */
        .desktop-grid {
            position: absolute;
            top: 40px;
            left: 40px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            z-index: 10;
        }

        .shortcut {
            width: 90px;
            text-align: center;
            cursor: pointer;
            transition: 0.2s;
            padding: 10px;
            border-radius: 12px;
        }

        .shortcut:hover { background: rgba(255, 255, 255, 0.1); transform: scale(1.05); }

        .icon-container { width: 54px; height: 54px; margin: 0 auto 8px; position: relative; }
        .app-icon { width: 100%; height: 100%; object-fit: contain; border-radius: 8px; }

        .shortcut span { font-size: 11px; font-weight: 600; text-shadow: 0 2px 4px rgba(0,0,0,1); }

        /* Floating Widget */
        .floating-widget {
            position: fixed;
            top: 30px;
            right: 30px;
            width: 300px;
            background: var(--glass-bg);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 28px;
            padding: 25px;
            box-shadow: 0 40px 80px rgba(0,0,0,0.6);
            z-index: 1000;
        }

        .timer-display {
            font-size: 3.8rem;
            font-weight: 900;
            font-family: 'Consolas', monospace;
            color: var(--accent-green);
            text-align: center;
            line-height: 1;
            margin-bottom: 15px;
            text-shadow: 0 0 20px rgba(34, 197, 94, 0.4);
        }

        .btn-action {
            width: 100%;
            padding: 14px;
            border-radius: 14px;
            border: none;
            background: rgba(255,255,255,0.05);
            color: white;
            font-weight: 800;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transition: all 0.2s;
            text-transform: uppercase;
            font-size: 12px;
        }

        .btn-action:hover { background: rgba(255,255,255,0.15); }

        /* Order Modal */
        #order-modal {
            position: fixed;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%) scale(0.9);
            width: 850px;
            background: #fff;
            border-radius: 30px;
            display: none;
            z-index: 5000;
            color: #1e293b;
            box-shadow: 0 40px 100px rgba(0,0,0,0.8);
            overflow: hidden;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        #order-modal.active { display: block; transform: translate(-50%, -50%) scale(1); }

        .modal-body-split { display: grid; grid-template-columns: 1fr 320px; height: 600px; }
        .menu-section { padding: 25px; overflow-y: auto; background: #fff; }
        .cart-section { background: #f8fafc; padding: 25px; display: flex; flex-direction: column; }
        
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 20px; }
        .menu-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 15px; cursor: pointer; text-align: center; transition: 0.3s; }
        .menu-card:hover { transform: translateY(-5px); border-color: var(--accent-blue); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .menu-card img { width: 100%; height: 110px; object-fit: cover; border-radius: 15px; margin-bottom: 10px; }

        .cart-list { flex: 1; overflow-y: auto; }
        .cart-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px dashed #e2e8f0; }

        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); backdrop-filter: blur(5px); display: none; z-index: 4999; }

        /* Taskbar */
        .taskbar {
            position: fixed;
            bottom: 0; left: 0; width: 100%; height: 45px;
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(15px);
            display: flex;
            align-items: center;
            padding: 0 20px;
            justify-content: space-between;
            z-index: 2000;
        }

        #lock-shutter { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #000; z-index: 99999; display: none; opacity: 0; transition: 0.4s; }
    </style>
</head>
<body>

    <div id="lock-shutter"></div>
    <div class="bg-overlay"></div>

    <!-- Desktop Icons -->
    <div class="desktop-grid">
        <div class="shortcut" onclick="alert('Launching Valorant...')">
            <div class="icon-container"><img src="../valorant.png" class="app-icon"></div>
            <span>Valorant</span>
        </div>
        <div class="shortcut" onclick="alert('Launching League...')">
            <div class="icon-container"><img src="../lol.png" class="app-icon"></div>
            <span>League</span>
        </div>
        <div class="shortcut" onclick="alert('Launching Dota 2...')">
            <div class="icon-container"><img src="../dota.png" class="app-icon"></div>
            <span>Dota 2</span>
        </div>
        <div class="shortcut">
            <div class="icon-container"><i data-lucide="trash-2" style="width: 48px; height: 48px; color: rgba(255,255,255,0.3);"></i></div>
            <span>Recycle Bin</span>
        </div>
    </div>

    <!-- Floating Widget -->
    <div class="floating-widget">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="badge bg-primary rounded-pill px-3">STATION 04</div>
            <span class="text-success small fw-bold">• ONLINE</span>
        </div>
        
        <div id="timer-display" class="timer-display">59:58</div>
        
        <div class="p-3 rounded-4 mb-3" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05);">
            <div class="d-flex justify-content-between small mb-1">
                <span class="text-white-50">User</span>
                <span class="fw-bold" id="player-username">admin</span>
            </div>
            <div class="d-flex justify-content-between small">
                <span class="text-white-50">Balance</span>
                <span class="text-info fw-bold" id="player-balance">₱100.00</span>
            </div>
        </div>

        <button class="btn-action" style="background: linear-gradient(135deg, #3b82f6, #2563eb);" onclick="openOrderMenu()">
            <i data-lucide="shopping-cart" style="width: 18px;"></i> ORDER FOOD
        </button>
        
        <button class="btn-action" style="background: rgba(59, 130, 246, 0.1); color: var(--accent-blue); border: 1px solid rgba(59, 130, 246, 0.2);" onclick="triggerLock()">
            <i data-lucide="lock" style="width: 18px;"></i> AFK LOCK
        </button>
        
        <button class="btn-action text-danger mt-3" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2);" onclick="window.location.href='../index.php'">
            <i data-lucide="power" style="width: 18px;"></i> LOG OUT
        </button>
    </div>

    <!-- Taskbar -->
    <div class="taskbar">
        <div class="d-flex align-items-center gap-4">
            <i data-lucide="layout-grid" style="color: var(--accent-blue); width: 22px;"></i>
            <i data-lucide="folder" style="color: #f59e0b; width: 20px;"></i>
        </div>
        <div class="d-flex align-items-center gap-3 text-white-50">
            <i data-lucide="wifi" style="width: 16px;"></i>
            <div class="text-end" style="font-size: 11px; line-height: 1.1;">
                <div class="fw-bold text-white"><?php echo date('H:i'); ?></div>
                <div style="font-size: 9px;"><?php echo date('m/d/Y'); ?></div>
            </div>
        </div>
    </div>

    <!-- Order Modal (Restored) -->
    <div id="order-overlay" class="modal-overlay" onclick="closeOrderMenu()"></div>
    <div id="order-modal">
        <div class="modal-header p-3 px-4 bg-light border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">STUXZ Cafe Menu</h5>
            <i data-lucide="x" style="cursor: pointer;" onclick="closeOrderMenu()"></i>
        </div>
        <div class="modal-body-split">
            <div class="menu-section" id="menu-items-container"></div>
            <div class="cart-section">
                <h6 class="fw-bold mb-3">My Cart</h6>
                <div class="cart-list" id="cart-items-container">
                    <div class="text-center text-muted mt-5">Your cart is empty</div>
                </div>
                <div class="border-top pt-3 mt-3">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold">Total:</span>
                        <span class="fw-bold text-primary" id="cart-total">₱0.00</span>
                    </div>
                    <button id="btn-checkout" class="btn btn-primary w-100 p-3 fw-bold rounded-3" disabled onclick="checkout()">PLACE ORDER</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/client-timer.js"></script>
    <script>
        if (typeof lucide !== 'undefined') lucide.createIcons();
        let currentCart = [];

        function triggerLock() {
            const shutter = document.getElementById('lock-shutter');
            shutter.style.display = 'block';
            setTimeout(() => { shutter.style.opacity = '1'; setTimeout(() => { window.location.href = 'afk_blur_screen.php'; }, 400); }, 10);
        }

        function openOrderMenu() {
            document.getElementById('order-modal').classList.add('active');
            document.getElementById('order-overlay').style.display = 'block';
            loadMenu();
        }

        function closeOrderMenu() {
            document.getElementById('order-modal').classList.remove('active');
            document.getElementById('order-overlay').style.display = 'none';
        }

        function loadMenu() {
            const container = document.getElementById('menu-items-container');
            container.innerHTML = '<div class="menu-grid" id="actual-grid"></div>';
            const grid = document.getElementById('actual-grid');

            fetch('../actions/get_inventory.php')
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                        let html = '';
                        data.data.forEach(item => {
                            const isOut = item.stock_quantity <= 0;
                            html += `<div class="menu-card ${isOut ? 'opacity-50' : ''}" onclick="${isOut ? '' : `addToCart(${item.id}, '${item.item_name}', ${item.price})`}">
                                <img src="../assets/img/${item.item_image}" onerror="this.src='https://via.placeholder.com/150?text=Food'">
                                <div class="fw-bold text-dark small mb-1">${item.item_name}</div>
                                <div class="text-primary fw-black">₱${parseFloat(item.price).toFixed(2)}</div>
                            </div>`;
                        });
                        grid.innerHTML = html;
                    }
                });
        }

        function addToCart(id, name, price) { currentCart.push({id, name, price}); renderCart(); }
        function removeFromCart(index) { currentCart.splice(index, 1); renderCart(); }

        function renderCart() {
            const container = document.getElementById('cart-items-container');
            const totalDisplay = document.getElementById('cart-total');
            const checkoutBtn = document.getElementById('btn-checkout');
            if(currentCart.length === 0) {
                container.innerHTML = '<div class="text-center text-muted mt-5">Your cart is empty</div>';
                totalDisplay.innerText = '₱0.00'; checkoutBtn.disabled = true; return;
            }
            let html = ''; let total = 0;
            currentCart.forEach((item, index) => {
                total += item.price;
                html += `<div class="cart-item"><span>${item.name}</span><div class="d-flex gap-2"><span class="fw-bold">₱${item.price}</span><i data-lucide="trash-2" style="width: 14px; color: #ef4444; cursor: pointer;" onclick="removeFromCart(${index})"></i></div></div>`;
            });
            container.innerHTML = html; totalDisplay.innerText = `₱${total.toFixed(2)}`;
            checkoutBtn.disabled = false; lucide.createIcons();
        }

        function checkout() {
            let total = currentCart.reduce((sum, item) => sum + item.price, 0);
            if(confirm(`Place order for ₱${total.toFixed(2)}?`)) {
                const formData = new FormData();
                formData.append('cart_data', JSON.stringify(currentCart));
                fetch('../actions/process_order.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                        alert('Order successful!'); currentCart = []; renderCart(); closeOrderMenu();
                        document.getElementById('player-balance').innerText = `₱${data.new_balance.toFixed(2)}`;
                    } else alert('Error: ' + data.message);
                });
            }
        }
    </script>
</body>
</html>
