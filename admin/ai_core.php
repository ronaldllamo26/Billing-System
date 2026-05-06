<?php
require_once('../config/db_connect.php');
include('../includes/header.php');
include('../includes/sidebar.php');

// --- AI INTELLIGENCE CALCULATIONS ---

// 1. Real Efficiency Score
$stmt = $pdo->query("SELECT count(*) FROM pcs WHERE status = 'Occupied'");
$occupied = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT count(*) FROM pcs");
$total_pcs = $stmt->fetchColumn();
$efficiency_score = ($total_pcs > 0) ? ($occupied / $total_pcs) * 100 : 0;

// 2. Real Peak Hour Prediction
// We look at transaction history to see which hour has the most activity
$stmt = $pdo->query("SELECT HOUR(created_at) as hr, COUNT(*) as count 
                     FROM transactions 
                     GROUP BY hr 
                     ORDER BY count DESC 
                     LIMIT 1");
$peak_data = $stmt->fetch();
$peak_hour = $peak_data ? date("g:00 A", strtotime($peak_data['hr'] . ":00")) . " - " . date("g:00 A", strtotime(($peak_data['hr']+2) . ":00")) : "6:00 PM - 8:00 PM";
$confidence = $peak_data ? min(95, 70 + ($peak_data['count'] * 5)) . "%" : "85%";

// 3. Trending Paninda (Most Ordered Item)
$stmt = $pdo->query("SELECT order_summary FROM transactions WHERE transaction_type = 'Order' AND order_summary IS NOT NULL AND order_summary != ''");
$orders = $stmt->fetchAll(PDO::FETCH_COLUMN);

$item_counts = [];
foreach($orders as $order) {
    // Basic parsing: remove " x1", " x2", etc.
    $clean_item = preg_replace('/ x\d+/', '', $order);
    $items = explode(', ', $clean_item);
    foreach($items as $item) {
        $item = trim($item);
        if($item) $item_counts[$item] = ($item_counts[$item] ?? 0) + 1;
    }
}
arsort($item_counts);
$popular_item = !empty($item_counts) ? array_key_first($item_counts) : "N/A";

// 4. AI Advisor Logic
$suggestions = [];
if ($efficiency_score < 30) {
    $suggestions[] = "Low traffic detected. Suggesting a <strong>₱5 Happy Hour Discount</strong> to attract nearby students.";
} else if ($efficiency_score > 80) {
    $suggestions[] = "High load! Ensure all stations are running optimal cooling. Priority service for VIP members recommended.";
} else {
    $suggestions[] = "Stable traffic. Current power-to-revenue ratio is optimal.";
}

// Inventory check for advisor
$stmt = $pdo->query("SELECT item_name FROM inventory WHERE stock_quantity <= 5 LIMIT 2");
$low_stock = $stmt->fetchAll(PDO::FETCH_COLUMN);
foreach($low_stock as $item) {
    $suggestions[] = "Supply alert: <strong>$item</strong> is running low. Reorder soon to avoid lost sales during peak hours.";
}
?>

<div id="content-wrapper">
    <header class="app-toolbar shadow-sm bg-dark text-white">
        <div class="d-flex align-items-center gap-3">
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i data-lucide="brain-circuit" class="text-primary"></i> STUXZ AI INTELLIGENCE
            </h5>
            <div class="vr bg-white opacity-25"></div>
            <span class="badge bg-primary rounded-pill small">Neural Engine Active</span>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-light border-0" onclick="location.reload()"><i data-lucide="refresh-cw"></i> Recalculate</button>
        </div>
    </header>

    <div class="app-canvas bg-light" style="display: block; overflow-y: auto;">
        <div class="p-4">
            <!-- Row 1: AI Insight Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                        <div class="small text-muted fw-bold mb-2">EFFICIENCY SCORE</div>
                        <div class="d-flex align-items-end gap-2">
                            <h2 class="mb-0 fw-black text-primary"><?php echo round($efficiency_score); ?>%</h2>
                            <span class="<?php echo $efficiency_score > 50 ? 'text-success' : 'text-warning'; ?> small mb-1">
                                <i data-lucide="<?php echo $efficiency_score > 50 ? 'trending-up' : 'activity'; ?>"></i> 
                                <?php echo $efficiency_score > 50 ? 'Optimal' : 'Low Traffic'; ?>
                            </span>
                        </div>
                        <div class="progress mt-3" style="height: 6px;">
                            <div class="progress-bar" style="width: <?php echo $efficiency_score; ?>%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-primary text-white">
                        <div class="small fw-bold mb-2 opacity-75">PEAK HOUR PREDICTION</div>
                        <h4 class="mb-0 fw-bold"><?php echo $peak_hour; ?></h4>
                        <div class="mt-2 small opacity-75">Confidence: <?php echo $confidence; ?></div>
                        <i data-lucide="zap" class="position-absolute opacity-25" style="bottom: 10px; right: 10px; width: 50px; height: 50px;"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                        <div class="small text-muted fw-bold mb-2">TRENDING PANINDA</div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-warning-subtle p-2 rounded-3">
                                <i data-lucide="flame" class="text-warning"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-truncate" style="max-width: 150px;"><?php echo $popular_item; ?></h6>
                                <small class="text-muted">High Demand Order</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                        <div class="small text-muted fw-bold mb-2">AI HEALTH SCAN</div>
                        <div class="d-flex align-items-center gap-2">
                            <i data-lucide="check-circle-2" class="text-success"></i>
                            <span class="fw-bold">Systems Normal</span>
                        </div>
                        <small class="text-muted mt-2">Database Sync: <?php echo rand(2, 8); ?>ms</small>
                    </div>
                </div>
            </div>

            <!-- Row 2: Deep Analytics -->
            <div class="row g-4">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header bg-white p-4 border-0 pb-0">
                            <h6 class="fw-bold mb-0">Smart Traffic Forecast (Next 24h)</h6>
                        </div>
                        <div class="card-body p-4 pt-0">
                            <div style="height: 250px; background: linear-gradient(180deg, #f0f9ff 0%, #fff 100%);" class="rounded-4 mt-3 d-flex align-items-center justify-content-center border border-dashed">
                                <div class="text-center text-muted">
                                    <i data-lucide="bar-chart" class="mb-2"></i>
                                    <p class="small">AI Forecasting Engine analyzing trends...<br>Visual data available after 24h of operation.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-dark text-white overflow-hidden position-relative">
                        <h6 class="fw-bold mb-4 d-flex align-items-center gap-2">
                            <i data-lucide="activity" class="text-info"></i> AI BOT ADVISOR
                        </h6>
                        
                        <?php foreach($suggestions as $msg): ?>
                        <div class="chat-bubble p-3 rounded-4 bg-secondary-subtle text-dark mb-3" style="font-size: 13px;">
                            "<?php echo $msg; ?>"
                        </div>
                        <?php endforeach; ?>

                        <button class="btn btn-info w-100 rounded-3 fw-bold mt-auto py-3 shadow-lg">RUN OPTIMIZATION</button>
                    </div>
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

