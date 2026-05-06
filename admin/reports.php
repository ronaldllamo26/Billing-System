<?php
require_once('../config/db_connect.php');
include('../includes/header.php');
include('../includes/sidebar.php');

// Fetch Stats
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT SUM(amount) as total FROM transactions WHERE DATE(created_at) = ? AND status = 'Completed'");
$stmt->execute([$today]);
$today_revenue = $stmt->fetchColumn() ?: 0;

$stmt = $pdo->query("SELECT transaction_type, SUM(amount) as total FROM transactions WHERE status = 'Completed' GROUP BY transaction_type");
$breakdown = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Recent Transactions
$stmt = $pdo->query("SELECT t.*, u.username FROM transactions t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC LIMIT 20");
$recent_tx = $stmt->fetchAll();
?>

<div id="content-wrapper">
    <header class="app-toolbar shadow-sm bg-dark text-white">
        <div class="d-flex align-items-center gap-3">
            <h5 class="mb-0 fw-bold">REVENUE & ANALYTICS</h5>
            <div class="vr bg-white opacity-25"></div>
            <span class="badge bg-success rounded-pill small">Real-time Data</span>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-light border-0" onclick="window.print()"><i data-lucide="printer"></i> Export PDF</button>
        </div>
    </header>

    <div class="app-canvas bg-light" style="display: block; overflow-y: auto;">
        <div class="p-4">
            <!-- Summary Row -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-primary text-white overflow-hidden position-relative">
                        <div class="small fw-bold opacity-75 mb-1">TODAY'S REVENUE</div>
                        <h1 class="mb-0 fw-black">₱<?php echo number_format($today_revenue, 2); ?></h1>
                        <i data-lucide="banknote" class="position-absolute opacity-25" style="bottom: -10px; right: -10px; width: 100px; height: 100px;"></i>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <div class="small text-muted fw-bold mb-3">REVENUE BREAKDOWN</div>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="text-primary fw-black h4 mb-0">₱<?php echo number_format($breakdown['Rental'] ?? 0, 2); ?></div>
                                <small class="text-muted fw-bold">PC RENTALS</small>
                            </div>
                            <div class="col-4 border-start">
                                <div class="text-success fw-black h4 mb-0">₱<?php echo number_format($breakdown['Top-up'] ?? 0, 2); ?></div>
                                <small class="text-muted fw-bold">MEMBER TOP-UPS</small>
                            </div>
                            <div class="col-4 border-start">
                                <div class="text-warning fw-black h4 mb-0">₱<?php echo number_format($breakdown['Order'] ?? 0, 2); ?></div>
                                <small class="text-muted fw-bold">FOOD & ORDERS</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction Table -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white p-4 border-0 pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">Recent Transactions History</h6>
                    <span class="small text-muted">Showing last 20 entries</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0" style="font-size: 13px;">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 border-0">Date & Time</th>
                                <th class="py-3 border-0">Customer</th>
                                <th class="py-3 border-0">Type</th>
                                <th class="py-3 border-0">Amount</th>
                                <th class="py-3 border-0">Status</th>
                                <th class="py-3 text-end pe-4 border-0">Summary</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_tx as $tx): ?>
                            <tr>
                                <td class="ps-4 align-middle text-muted"><?php echo date('M d, g:i A', strtotime($tx['created_at'])); ?></td>
                                <td class="align-middle fw-bold"><?php echo htmlspecialchars($tx['username']); ?></td>
                                <td class="align-middle">
                                    <span class="badge bg-light text-dark border fw-bold"><?php echo strtoupper($tx['transaction_type']); ?></span>
                                </td>
                                <td class="align-middle fw-black text-primary">₱<?php echo number_format($tx['amount'], 2); ?></td>
                                <td class="align-middle">
                                    <?php if($tx['status'] == 'Completed'): ?>
                                        <span class="text-success small fw-bold"><i data-lucide="check-circle" style="width:12px;"></i> Completed</span>
                                    <?php else: ?>
                                        <span class="text-warning small fw-bold"><i data-lucide="clock" style="width:12px;"></i> <?php echo $tx['status']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4 align-middle text-muted italic" style="font-size: 11px;">
                                    <?php echo htmlspecialchars($tx['order_summary'] ?: '-'); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
});
</script>

<?php include('../includes/footer.php'); ?>
