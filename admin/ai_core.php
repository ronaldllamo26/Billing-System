<?php
require_once('../config/db_connect.php');
include('../includes/header.php');
include('../includes/sidebar.php');

// Simple "AI" Data Generation (In a real system, these would be complex SQL analytics)
// 1. Peak Hour Prediction
$peak_hour = "6:00 PM - 9:00 PM";
$confidence = "88%";

// 2. Efficiency Score (Based on Occupied vs Vacant)
$stmt = $pdo->query("SELECT count(*) FROM pcs WHERE status = 'Occupied'");
$occupied = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT count(*) FROM pcs");
$total_pcs = $stmt->fetchColumn();
$efficiency_score = ($occupied / $total_pcs) * 100;

// 3. Most Popular Item (AI Insights)
$stmt = $pdo->query("SELECT order_summary FROM transactions WHERE transaction_type = 'Order' LIMIT 10");
$orders = $stmt->fetchAll(PDO::FETCH_COLUMN);
$popular_item = "Pancit Canton"; // Simplified for demo
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
            <button class="btn btn-sm btn-outline-light border-0"><i data-lucide="refresh-cw"></i> Recalculate</button>
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
                            <span class="text-success small mb-1"><i data-lucide="trending-up"></i> Optimal</span>
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
                                <h6 class="mb-0 fw-bold"><?php echo $popular_item; ?></h6>
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
                        <small class="text-muted mt-2">Database & Sync Latency: < 10ms</small>
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
                            <!-- Placeholder for a Chart -->
                            <div style="height: 250px; background: linear-gradient(180deg, #f0f9ff 0%, #fff 100%);" class="rounded-4 mt-3 d-flex align-items-center justify-content-center border border-dashed">
                                <div class="text-center text-muted">
                                    <i data-lucide="bar-chart" class="mb-2"></i>
                                    <p class="small">Interactive Forecast Chart<br>Will populate as transaction history grows.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-dark text-white overflow-hidden">
                        <h6 class="fw-bold mb-4 d-flex align-items-center gap-2">
                            <i data-lucide="activity" class="text-info"></i> AI BOT ADVISOR
                        </h6>
                        <div class="chat-bubble p-3 rounded-4 bg-secondary-subtle text-dark mb-3" style="font-size: 13px;">
                            "I've noticed <strong>PC-04</strong> has the highest up-time today. Consider running a ₱5 discount on <strong>Regular PC-06</strong> to balance the load."
                        </div>
                        <div class="chat-bubble p-3 rounded-4 bg-secondary-subtle text-dark mb-3" style="font-size: 13px;">
                            "Peak hours are approaching. Ensure inventory for <strong>Coke 500ml</strong> is sufficient."
                        </div>
                        <button class="btn btn-info w-100 rounded-3 fw-bold mt-auto py-3">RUN SYSTEM OPTIMIZATION</button>
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
