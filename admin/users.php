<?php
require_once('../config/db_connect.php');
include('../includes/header.php');
include('../includes/sidebar.php');

// Fetch all regular users (members)
$stmt = $pdo->query("SELECT * FROM users WHERE role = 'User' ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<div id="content-wrapper">
    <header class="app-toolbar shadow-sm">
        <div class="d-flex align-items-center gap-3">
            <h5 class="mb-0 fw-bold">Member Accounts</h5>
            <div class="vr"></div>
            <button class="btn btn-sm btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i data-lucide="user-plus" style="width: 14px;"></i> Register Member
            </button>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-light text-dark border">Total Members: <?php echo count($users); ?></span>
        </div>
    </header>

    <div class="app-canvas" style="display: block; overflow-y: auto;">
        <div class="station-container p-0 overflow-hidden">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 border-0">Member Name</th>
                        <th class="py-3 border-0">Balance</th>
                        <th class="py-3 border-0">Member Since</th>
                        <th class="py-3 text-end pe-4 border-0">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="ps-4 align-middle">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-circle bg-primary text-white" style="width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px;">
                                    <?php echo strtoupper(substr($user['username'], 0, 2)); ?>
                                </div>
                                <span class="fw-bold"><?php echo htmlspecialchars($user['username']); ?></span>
                            </div>
                        </td>
                        <td class="align-middle fw-bold text-success">₱<?php echo number_format($user['balance'], 2); ?></td>
                        <td class="align-middle text-muted" style="font-size: 12px;">
                            <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                        </td>
                        <td class="text-end pe-4 align-middle">
                            <button class="btn btn-sm btn-outline-success fw-bold me-2" onclick="openTopupModal(<?php echo $user['id']; ?>, '<?php echo $user['username']; ?>')">
                                <i data-lucide="wallet" style="width: 14px;"></i> TOP-UP
                            </button>
                            <button class="btn btn-sm btn-light border text-danger">
                                <i data-lucide="trash-2" style="width: 14px;"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Register User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold">Register New Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../actions/register_member.php" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Desired Username</label>
                        <input type="text" name="username" class="form-control bg-light border-0 p-3" placeholder="e.g. GamerX123" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Password</label>
                        <input type="password" name="password" class="form-control bg-light border-0 p-3" value="123456" required>
                        <small class="text-muted">Default: 123456</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Initial Top-up (₱)</label>
                        <input type="number" name="balance" class="form-control bg-light border-0 p-3" value="0">
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-primary w-100 p-3 rounded-3 fw-bold">CREATE MEMBER ACCOUNT</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Top-up Modal -->
<div class="modal fade" id="topupModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold">Account Top-up</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../actions/topup_member.php" method="POST">
                <input type="hidden" name="user_id" id="topup-user-id">
                <div class="modal-body p-4">
                    <p class="text-muted">Adding balance to: <strong id="topup-username" class="text-dark"></strong></p>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Amount to Add (₱)</label>
                        <input type="number" name="amount" class="form-control bg-light border-0 p-3" placeholder="50.00" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-success w-100 p-3 rounded-3 fw-bold">CONFIRM TOP-UP</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openTopupModal(id, username) {
    document.getElementById('topup-user-id').value = id;
    document.getElementById('topup-username').innerText = username;
    new bootstrap.Modal(document.getElementById('topupModal')).show();
}

document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
});
</script>

<?php include('../includes/footer.php'); ?>
