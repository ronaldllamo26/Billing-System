<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STUXZ | Station Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            margin: 0;
            height: 100vh;
            background: #0a0e17 url('../assets/img/wallpaper.png') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', sans-serif;
            overflow: hidden;
            color: white;
        }

        /* Desktop Grid */
        .desktop-grid {
            position: absolute;
            top: 20px;
            left: 20px;
            display: flex;
            flex-direction: column;
            flex-wrap: wrap;
            height: calc(100vh - 100px);
            align-content: flex-start;
            gap: 10px;
        }

        .shortcut {
            width: 80px;
            text-align: center;
            cursor: pointer;
            padding: 10px 5px;
            border-radius: 4px;
            transition: 0.1s;
        }

        .shortcut:hover { background: rgba(255, 255, 255, 0.1); }

        .icon-container {
            position: relative;
            width: 48px;
            height: 48px;
            margin: 0 auto 5px;
        }

        .app-icon { width: 100%; height: 100%; object-fit: contain; }

        .shortcut-arrow {
            position: absolute;
            bottom: -2px;
            left: -2px;
            width: 16px;
            height: 16px;
            background: white;
            border: 1px solid #999;
            border-radius: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .shortcut-arrow::after { content: '↗'; color: #000; font-size: 10px; font-weight: bold; }

        .shortcut span {
            font-size: 11px;
            text-shadow: 1px 1px 2px rgba(0,0,0,1);
            display: block;
        }

        /* Taskbar */
        .taskbar {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 40px;
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            padding: 0 10px;
            justify-content: space-between;
            z-index: 2000;
        }

        /* Floating Widget */
        .floating-widget {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 280px;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 25px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .timer-display {
            font-size: 3.5rem;
            font-weight: 900;
            font-family: 'Consolas', monospace;
            color: #34d399;
            text-align: center;
            text-shadow: 0 0 20px rgba(52, 211, 153, 0.3);
        }

        .btn-action {
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            border: none;
            background: rgba(255,255,255,0.08);
            color: white;
            font-weight: 700;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-action:hover { background: rgba(255,255,255,0.15); }

        /* Order Modal with Cart GUI */
        #order-modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.9);
            width: 800px;
            background: #ffffff;
            border-radius: 24px;
            display: none;
            z-index: 5000;
            color: #1e293b;
            box-shadow: 0 40px 100px rgba(0,0,0,0.8);
            overflow: hidden;
        }

        #order-modal.active { display: block; transform: translate(-50%, -50%) scale(1); }

        .modal-body-split { display: grid; grid-template-columns: 1fr 320px; height: 500px; }
        .menu-section { padding: 20px; overflow-y: auto; border-right: 1px solid #e2e8f0; }
        .cart-section { background: #f8fafc; padding: 20px; display: flex; flex-direction: column; }
        
        .menu-item {
            display: flex;
            gap: 15px;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 10px;
            background: #fff;
            border: 1px solid #e2e8f0;
            transition: 0.2s;
        }

        .menu-item-img { width: 60px; height: 60px; border-radius: 8px; object-fit: cover; }
        .cart-list { flex: 1; overflow-y: auto; }
        .cart-item { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px dashed #cbd5e1; }

        .modal-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(5px);
            display: none;
            z-index: 4999;
        }
    </style>
</head>
<body>

    <!-- Desktop Icons -->
    <div class="desktop-grid">
        <div class="shortcut">
            <div class="icon-container">
                <img src="../assets/img/valorant_icon_shortcut_1778071265293.png" class="app-icon">
                <div class="shortcut-arrow"></div>
            </div>
            <span>Valorant</span>
        </div>
        <div class="shortcut">
            <div class="icon-container">
                <img src="../assets/img/chrome_icon_shortcut_1778071286078.png" class="app-icon">
                <div class="shortcut-arrow"></div>
            </div>
            <span>Google Chrome</span>
        </div>
        <div class="shortcut">
            <div class="icon-container">
                <img src="../assets/img/roblox_icon_shortcut_1778071302782.png" class="app-icon">
                <div class="shortcut-arrow"></div>
            </div>
            <span>Roblox Player</span>
        </div>
        <div class="shortcut">
            <div class="icon-container">
                <img src="../assets/img/this_pc_icon_win11_1778071320030.png" class="app-icon">
            </div>
            <span>This PC</span>
        </div>
        <div class="shortcut">
            <div class="icon-container">
                <i data-lucide="trash-2" style="width: 40px; height: 40px; color: #cbd5e1;"></i>
            </div>
            <span>Recycle Bin</span>
        </div>
    </div>

    <!-- Floating Widget -->
    <div class="floating-widget">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <small class="fw-bold">STATION 04</small>
            <small class="text-secondary fw-bold">VIP</small>
        </div>
        <div id="timer-display" class="timer-display">59:59</div>
        <div class="d-flex justify-content-between small text-secondary">
            <span>User: <strong>GamerXPro</strong></span>
            <span>Bal: <strong id="player-balance" class="text-info">₱150</strong></span>
        </div>
        <button class="btn-action" style="background: linear-gradient(135deg, #3b82f6, #2563eb); margin-top: 20px;" onclick="openOrderMenu()">
            <i data-lucide="shopping-cart" style="width: 18px;"></i> ORDER FOOD
        </button>
        <button class="btn-action" onclick="window.location.href='afk_blur_screen.php'">
            <i data-lucide="lock" style="width: 18px;"></i> AFK LOCK
        </button>
        <button class="btn-action text-danger mt-3" style="background: rgba(239, 68, 68, 0.1);" onclick="window.location.href='../index.php'">
            <i data-lucide="power" style="width: 18px;"></i> LOG OUT
        </button>
    </div>

    <!-- Taskbar -->
    <div class="taskbar">
        <div class="d-flex align-items-center gap-3">
            <i data-lucide="layout-grid" style="color: #3b82f6; width: 18px;"></i>
            <i data-lucide="search" style="color: #64748b; width: 16px;"></i>
            <div class="vr mx-2" style="background: #333; height: 20px;"></div>
            <i data-lucide="chrome" style="color: #60a5fa; width: 18px;"></i>
            <i data-lucide="folder" style="color: #f59e0b; width: 18px;"></i>
        </div>
        <div class="d-flex align-items-center gap-3 text-secondary">
            <i data-lucide="wifi" style="width: 16px;"></i>
            <i data-lucide="volume-2" style="width: 16px;"></i>
            <div class="text-end" style="font-size: 10px; line-height: 1;">
                <div class="fw-bold text-white"><?php echo date('H:i'); ?></div>
                <div style="font-size: 8px;"><?php echo date('m/d/Y'); ?></div>
            </div>
        </div>
    </div>

    <!-- Order Modal -->
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
        lucide.createIcons();
        let currentCart = [];
        let playerBalance = 150;

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
            fetch('../actions/get_inventory.php')
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                        let html = '';
                        data.data.forEach(item => {
                            const isOut = item.stock_quantity <= 0;
                            html += `
                                <div class="menu-item ${isOut ? 'opacity-50' : ''}">
                                    <img src="../assets/img/${item.item_image}" class="menu-item-img">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold" style="font-size: 14px;">${item.item_name}</div>
                                        <div class="text-primary fw-bold small">₱${item.price}</div>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary align-self-center" ${isOut ? 'disabled' : ''} onclick="addToCart(${item.id}, '${item.item_name}', ${item.price})">+ Add</button>
                                </div>
                            `;
                        });
                        container.innerHTML = html;
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
                totalDisplay.innerText = '₱0.00';
                checkoutBtn.disabled = true; return;
            }
            let html = ''; let total = 0;
            currentCart.forEach((item, index) => {
                total += item.price;
                html += `<div class="cart-item"><span>${item.name}</span><div class="d-flex gap-2"><span class="fw-bold">₱${item.price}</span><i data-lucide="trash-2" style="width: 14px; color: #ef4444; cursor: pointer;" onclick="removeFromCart(${index})"></i></div></div>`;
            });
            container.innerHTML = html;
            totalDisplay.innerText = `₱${total.toFixed(2)}`;
            checkoutBtn.disabled = false;
            lucide.createIcons();
        }

        function checkout() {
            let total = currentCart.reduce((sum, item) => sum + item.price, 0);
            
            if(confirm(`Place order for ₱${total.toFixed(2)}?`)) {
                const formData = new FormData();
                formData.append('cart_data', JSON.stringify(currentCart));

                fetch('../actions/process_order.php', { method: 'POST', body: formData })
                .then(res => res.text()) // Get as text first to debug
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        if(data.status === 'success') {
                            alert('Order successful! Admin notified.');
                            currentCart = []; renderCart(); closeOrderMenu();
                            // Update display balance immediately
                            document.getElementById('player-balance').innerText = `₱${data.new_balance.toFixed(2)}`;
                        } else {
                            alert('Order Failed: ' + data.message);
                        }
                    } catch(e) {
                        console.error('Server response was not JSON:', text);
                        alert('Critical Error: Server sent an invalid response.');
                    }
                })
                .catch(err => {
                    console.error('Fetch error:', err);
                    alert('Connection Error. Please check your network.');
                });
            }
        }
    </script>
</body>
</html>
