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
                <div class="pc-node <?php echo strtolower($pc['status']); ?>">
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
                            <div class="timer-label fw-bold">
                                <i data-lucide="clock" style="width: 12px;"></i> 
                                <?php 
                                    $seconds = $pc['time_remaining'];
                                    $h = floor($seconds / 3600);
                                    $m = floor(($seconds % 3600) / 60);
                                    $s = $seconds % 60;
                                    echo sprintf('%02d:%02d:%02d', $h, $m, $s);
                                ?>
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
                        
                        <!-- Real-time Time Preview -->
                        <div id="time-preview" class="mt-2 text-primary fw-bold small" style="display: none;">
                            <i data-lucide="zap" style="width: 14px;"></i> Customer gets: <span id="preview-text">00:00:00</span>
                        </div>

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

    document.addEventListener('DOMContentLoaded', () => {
        updateRateHighlight();
        lucide.createIcons();
    });
</script>

<?php include('../includes/footer.php'); ?>
