<?php
require_once('../config/db_connect.php');
include('../includes/header.php');
include('../includes/sidebar.php');

// Fetch PCs with Zone Info explicitly
$stmt = $pdo->query("SELECT p.*, u.username 
                     FROM pcs p 
                     LEFT JOIN users u ON p.current_user_id = u.id 
                     ORDER BY p.pc_number ASC");
$pcs = $stmt->fetchAll();
?>

<div id="content-wrapper">
    <header class="app-toolbar shadow-sm">
        <div class="d-flex align-items-center gap-3">
            <h5 class="mb-0 fw-bold">STUXZ System Core <small class="text-muted" style="font-size: 10px;">v1.0.7</small></h5>
            <div class="vr"></div>
            <div class="toolbar-stats d-flex gap-3 small fw-bold">
                <span class="text-primary">Occupied: <?php echo count(array_filter($pcs, fn($p) => $p['status'] == 'Occupied')); ?></span>
                <span class="text-success">Vacant: <?php echo count(array_filter($pcs, fn($p) => $p['status'] == 'Vacant')); ?></span>
            </div>
        </div>
        <div class="toolbar-actions">
            <button class="gui-btn" data-bs-toggle="modal" data-bs-target="#newSessionModal"><i data-lucide="plus"></i> New Session</button>
            <button class="gui-btn" onclick="location.reload()"><i data-lucide="refresh-cw"></i> Sync Stations</button>
        </div>
    </header>

    <div class="app-canvas">
        <div class="station-container">
            <div class="zone-header">STATION GRID</div>
            <div class="station-grid">
                <?php foreach($pcs as $pc): ?>
                <div class="pc-node <?php echo strtolower($pc['status']); ?>" 
                     id="pc-node-<?php echo $pc['id']; ?>"
                     onclick="manageStation(<?php echo $pc['id']; ?>, '<?php echo $pc['pc_number']; ?>', '<?php echo $pc['status']; ?>')"
                     style="cursor: pointer; transition: transform 0.2s;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="fw-bold">
                            <?php echo $pc['pc_number'] < 10 ? 'PC-0'.$pc['pc_number'] : 'PC-'.$pc['pc_number']; ?>
                            <small class="d-block text-muted" style="font-size: 9px;"><?php echo strtoupper($pc['pc_type']); ?></small>
                        </div>
                        <span class="status-badge"><?php echo strtoupper($pc['status']); ?></span>
                    </div>
                    <div class="node-info">
                        <?php if($pc['status'] == 'Occupied'): ?>
                            <div class="user-label text-primary fw-bold"><?php echo htmlspecialchars($pc['username'] ?? 'Guest'); ?></div>
                            <div class="timer-label fw-bold timer-display-admin" data-pc-id="<?php echo $pc['id']; ?>" data-seconds="<?php echo $pc['time_remaining']; ?>">
                                <i data-lucide="clock" style="width: 12px;"></i> 
                                <span class="time-display">
                                <?php 
                                    $seconds = $pc['time_remaining'];
                                    $h = floor($seconds / 3600);
                                    $m = floor(($seconds % 3600) / 60);
                                    $s = $seconds % 60;
                                    echo sprintf('%02d:%02d:%02d', $h, $m, $s);
                                ?>
                                </span>
                            </div>
                        <?php else: ?>
                            <div class="user-label text-muted">Ready for session</div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Right: Order Queue -->
        <div class="order-queue-panel">
            <div class="p-3 bg-light border-bottom">
                <h6 class="mb-0 fw-bold">ACTIVE ORDERS</h6>
            </div>
            <div id="active-orders-list" class="p-3"></div>
        </div>
    </div>
</div>

<!-- Intelligent Session Modal -->
<div class="modal fade" id="newSessionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold">Activate Station</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="sessionForm" onsubmit="event.preventDefault(); startSession();">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Select Station</label>
                        <select name="pc_id" id="pc_id" class="form-select bg-light border-0 p-3" onchange="updateRateHighlight()" required>
                            <?php foreach($pcs as $pc): ?>
                                <?php if($pc['status'] == 'Vacant'): ?>
                                    <option value="<?php echo $pc['id']; ?>" data-type="<?php echo $pc['pc_type']; ?>">
                                        PC-<?php echo str_pad($pc['pc_number'], 2, '0', STR_PAD_LEFT); ?> (<?php echo $pc['pc_type']; ?>)
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Customer Name</label>
                        <input type="text" name="username" id="username" class="form-control bg-light border-0 p-3" value="Guest" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Amount (₱)</label>
                        <input type="number" name="amount" id="amount" class="form-control bg-light border-0 p-3" placeholder="25.00" oninput="calculateTimePreview()" required>
                        
                        <!-- Intelligent Rate Cards -->
                        <div class="mt-3 p-3 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="row g-0">
                                <div id="rate-regular" class="col-6 border-end pe-3 transition-opacity">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div style="width: 8px; height: 8px; background: #3b82f6; border-radius: 50%;"></div>
                                        <strong style="font-size: 11px; color: #1e293b;">REGULAR</strong>
                                    </div>
                                    <div style="font-size: 12px; line-height: 1.6; color: #475569;">
                                        1H: ₱25 | 3H: ₱60<br>6H: ₱100
                                    </div>
                                </div>
                                <div id="rate-vip" class="col-6 ps-3 transition-opacity">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div style="width: 8px; height: 8px; background: #ef4444; border-radius: 50%;"></div>
                                        <strong style="font-size: 11px; color: #1e293b;">VIP ZONE</strong>
                                    </div>
                                    <div style="font-size: 12px; line-height: 1.6; color: #475569;">
                                        1H: ₱35 | 3H: ₱90<br>6H: ₱140
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-primary w-100 p-3 rounded-3 fw-bold shadow-sm">ACTIVATE NOW</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Station Management Modal (Command Center) -->
<div class="modal fade" id="stationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-7 p-4 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0 fw-black" id="m-station-name">STATION 04</h4>
                            <span class="badge bg-primary rounded-pill px-3" id="m-station-status">OCCUPIED</span>
                        </div>
                        
                        <div id="m-occupied-view" style="display: none;">
                            <div class="bg-light p-4 rounded-4 mb-4">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <small class="text-muted fw-bold">TIME REMAINING</small>
                                        <h1 class="mb-0 fw-black display-5" id="m-time-left">00:59:45</h1>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-outline-danger border-0" title="End Session" onclick="endSession()">
                                            <i data-lucide="power" style="width: 24px;"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary p-3 fw-bold rounded-3" onclick="openAddTime()">
                                    <i data-lucide="plus" class="me-2"></i> ADD TIME / TOP-UP
                                </button>
                            </div>
                        </div>

                        <div id="m-vacant-view" style="display: none;">
                            <p class="text-muted">This station is currently vacant. Start a new session to activate.</p>
                            <button class="btn btn-primary w-100 p-3 fw-bold rounded-3" onclick="openNewSessionFromModal()">
                                START NEW SESSION
                            </button>
                        </div>
                    </div>
                    <div class="col-md-5 bg-dark p-4 d-flex flex-column justify-content-center text-center">
                        <h6 class="text-white-50 mb-3 small fw-bold">REMOTE SCREEN PREVIEW</h6>
                        <div class="position-relative overflow-hidden rounded-3 border border-secondary border-opacity-50" style="aspect-ratio: 16/9; background: #000;">
                            <img id="screen-preview-img" src="https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=400&auto=format&fit=crop" class="w-100 h-100 object-fit-cover opacity-75">
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <i data-lucide="eye" style="width: 48px; height: 48px; color: rgba(255,255,255,0.2);"></i>
                            </div>
                            <div class="position-absolute bottom-0 start-0 w-100 p-2 bg-black bg-opacity-50 text-white" style="font-size: 8px;">
                                LIVE STREAMING... <span class="text-danger">●</span>
                            </div>
                        </div>
                        <button class="btn btn-outline-light btn-sm mt-3 border-0 opacity-50" onclick="refreshMonitor()">
                            <i data-lucide="refresh-cw" class="me-1" style="width: 12px;"></i> Refresh Feed
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/admin-ajax.js"></script>
<script>
    function updateRateHighlight() {
        const select = document.getElementById('pc_id');
        const type = select.options[select.selectedIndex].getAttribute('data-type');
        
        const reg = document.getElementById('rate-regular');
        const vip = document.getElementById('rate-vip');

        if(type === 'Regular') {
            reg.style.opacity = '1';
            vip.style.opacity = '0.3';
        } else {
            reg.style.opacity = '0.3';
            vip.style.opacity = '1';
        }
        calculateTimePreview(); // Recalculate when PC changes
    }

    function calculateTimePreview() {
        const amount = parseFloat(document.getElementById('amount').value) || 0;
        const select = document.getElementById('pc_id');
        if(!select.options[select.selectedIndex]) return;
        const type = select.options[select.selectedIndex].getAttribute('data-type');
        const preview = document.getElementById('time-preview');
        const previewText = document.getElementById('preview-text');

        if(amount <= 0) {
            preview.style.display = 'none';
            return;
        }

        let seconds = 0;
        if(type === 'Regular') {
            if(amount >= 100) seconds = 6 * 3600;
            else if(amount >= 60) seconds = 3 * 3600;
            else seconds = (amount / 25) * 3600;
        } else {
            if(amount >= 140) seconds = 6 * 3600;
            else if(amount >= 90) seconds = 3 * 3600;
            else seconds = (amount / 35) * 3600;
        }

        const h = Math.floor(seconds / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        const s = seconds % 60;
        
        previewText.innerText = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
        preview.style.display = 'block';
        lucide.createIcons();
    }

    function startSession() {
        const formData = new FormData();
        formData.append('pc_id', document.getElementById('pc_id').value);
        formData.append('username', document.getElementById('username').value);
        formData.append('amount', document.getElementById('amount').value);

        fetch('../actions/start_session.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
    }

    function startDashboardTimers() {
        setInterval(() => {
            document.querySelectorAll('.timer-display-admin').forEach(timer => {
                let seconds = parseInt(timer.getAttribute('data-seconds'));
                if (seconds > 0) {
                    seconds--;
                    timer.setAttribute('data-seconds', seconds);
                    
                    const h = Math.floor(seconds / 3600);
                    const m = Math.floor((seconds % 3600) / 60);
                    const s = seconds % 60;
                    
                    timer.innerText = 
                        `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                }
            });
        }, 1000);
    }

    const orderSound = new Audio('https://www.soundjay.com/buttons/sounds/button-3.mp3');
    let lastOrderCount = -1;

    let selectedPcId = null;

    function manageStation(id, number, status) {
        selectedPcId = id;
        document.getElementById('m-station-name').innerText = 'STATION ' + (number < 10 ? '0' + number : number);
        document.getElementById('m-station-status').innerText = status.toUpperCase();
        
        const occupiedView = document.getElementById('m-occupied-view');
        const vacantView = document.getElementById('m-vacant-view');
        
        if (status === 'Occupied') {
            occupiedView.style.display = 'block';
            vacantView.style.display = 'none';
            // Get time from the specific card
            const timeStr = document.querySelector(`.pc-node[onclick*="${id}"] .timer-display-admin`).innerText;
            document.getElementById('m-time-left').innerText = timeStr;
            refreshMonitor();
        } else {
            occupiedView.style.display = 'none';
            vacantView.style.display = 'block';
            document.getElementById('screen-preview-img').src = 'https://images.unsplash.com/photo-1580234811497-9bd7fd0f56e2?q=80&w=400&auto=format&fit=crop';
        }
        
        new bootstrap.Modal(document.getElementById('stationModal')).show();
        lucide.createIcons();
    }

    function refreshMonitor() {
        const previews = [
            'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=400&auto=format&fit=crop', // Gaming
            'https://images.unsplash.com/photo-1587620962725-abab7fe55159?q=80&w=400&auto=format&fit=crop', // Coding
            'https://images.unsplash.com/photo-1614332287897-cdc485fa562d?q=80&w=400&auto=format&fit=crop', // Browsing
            'https://images.unsplash.com/photo-1511512578047-dfb367046420?q=80&w=400&auto=format&fit=crop'  // VALORANT lookalike
        ];
        const random = previews[Math.floor(Math.random() * previews.length)];
        document.getElementById('screen-preview-img').src = random;
    }

    function openNewSessionFromModal() {
        // Pre-select PC in the existing new session modal
        document.querySelector('select[name="pc_id"]').value = selectedPcId;
        bootstrap.Modal.getInstance(document.getElementById('stationModal')).hide();
        new bootstrap.Modal(document.getElementById('newSessionModal')).show();
    }

    function refreshOrders() {
        fetch('../actions/get_pending_orders.php')
            .then(res => res.json())
            .then(data => {
                const list = document.getElementById('active-orders-list');
                const orders = data.orders || [];

                if (orders.length > lastOrderCount && lastOrderCount !== -1) {
                    console.log('NEW ORDER DETECTED! Playing sound...');
                    orderSound.play().catch(e => console.log('Sound blocked by browser. Click anywhere on the page to enable sound.'));
                }
                lastOrderCount = orders.length;

                if(orders.length === 0) {
                    list.innerHTML = `
                        <div class="text-center text-muted p-5">
                            <div class="mb-3 opacity-25"><i data-lucide="coffee" style="width: 48px; height: 48px;"></i></div>
                            <p class="small fw-bold">Walang pending orders, pre.<br>Chill muna!</p>
                        </div>
                    `;
                    lucide.createIcons();
                    return;
                }

                list.innerHTML = orders.map(order => {
                    const items = order.order_summary.split(', ').map(i => `<div class="order-item-chip">${i}</div>`).join('');
                    return `
                        <div class="card border-0 shadow-sm mb-3 rounded-4 overflow-hidden animate__animated animate__fadeInRight" 
                             style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border: 1px solid #e2e8f0 !important;">
                            <div class="p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="pc-badge-mini">PC-${order.pc_number}</div>
                                        <div class="pulse-dot"></div>
                                    </div>
                                    <small class="text-muted fw-bold" style="font-size: 10px;">${order.time_ago || 'JUST NOW'}</small>
                                </div>
                                
                                <div class="mb-3 d-flex flex-wrap gap-1">
                                    ${items}
                                </div>

                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <div>
                                        <div class="small text-muted" style="font-size: 9px; font-weight: 800;">TOTAL AMOUNT</div>
                                        <div class="text-primary fw-black h5 mb-0">₱${parseFloat(order.amount).toFixed(2)}</div>
                                    </div>
                                    <button class="btn-serve-modern" onclick="completeOrder(${order.id})">
                                        <i data-lucide="check" style="width: 14px;"></i> SERVE
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
                lucide.createIcons();
            });
    }

    function completeOrder(id) {
        if(!confirm('Mark this order as served?')) return;
        const formData = new FormData();
        formData.append('order_id', id);
        fetch('../actions/complete_order.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') refreshOrders();
            });
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateRateHighlight();
        startDashboardTimers();
        refreshOrders();
        setInterval(refreshOrders, 10000); 
        lucide.createIcons();
    });
</script>

<?php include('../includes/footer.php'); ?>
